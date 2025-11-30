Шаг 1 — Краткая идея и структура

Мы будем сохранять прогресс в usermeta (таблица wp_usermeta) под ключом lesson_progress.

Структура lesson_progress (массив):

ключ = product_id (ID курса / товара)

значение = массив с ключами lesson_id => timestamp (когда посмотрел)

Когда пользователь нажмёт Play на видео, клиент (JS) отправит AJAX-запрос mark_lesson_watched на сервер.

Сервер сохранит запись в usermeta.

При выводе списка уроков (в sidebar или в loop) мы читаем usermeta и ставим класс completed / current.

Шаг 2 — Серверный код (в functions.php)

Вставь этот код в functions.php темы (или в файл плагина). Комментарии перед каждой строчкой объясняют, что и почему.

<?php
// -----------------------
// 1) РЕГИСТРАЦИЯ AJAX обработчика (для авторизованных)
// -----------------------

// Комментарий: подключаем обработчик для AJAX-запроса от залогиненных пользователей.
// Обработчик с хук 'wp_ajax_' . action
add_action( 'wp_ajax_mark_lesson_watched', 'mark_lesson_watched' );

// Комментарий: определение функции-обработчика AJAX, вызывается при action=mark_lesson_watched
function mark_lesson_watched() {
    // Комментарий: проверяем nonce для безопасности (nonce отправляется из JS). Если нет — завершаем.
    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'mark_lesson_watched_nonce' ) ) {
        // Комментарий: завершаем с ошибкой, если nonce невалиден.
        wp_die( 'Invalid nonce' );
    }

    // Комментарий: убедимся, что пользователь залогинен (мы храним прогресс только для зарегистрированных).
    if ( ! is_user_logged_in() ) {
        // Комментарий: завершаем если не залогинен.
        wp_die( 'Not logged in' );
    }

    // Комментарий: получаем ID текущего пользователя — от WP (целое число)
    $user_id = get_current_user_id();

    // Комментарий: получаем ID урока из параметров запроса, приводим к integer и очищаем
    $lesson_id = isset( $_GET['lesson_id'] ) ? intval( wp_unslash( $_GET['lesson_id'] ) ) : 0;

    // Комментарий: если урока нет — завершаем
    if ( ! $lesson_id ) {
        wp_die( 'No lesson id' );
    }

    // Комментарий: пытаемся получить связанный product_id из метаполя урока
    // Комментарий: предполагаем, что у тебя урок хранит связку в метаполе '_related_product' или '_product_id'
    // Комментарий: замени '_related_product' на ключ, который у тебя реально хранит связь
    $product_id = get_post_meta( $lesson_id, '_related_product', true );

    // Комментарий: если метаполе пустое, попытаемся ещё: может быть массив в _product_lessons у продукта, но тут проще остановиться
    if ( ! $product_id ) {
        wp_die( 'No product id' );
    }

    // Комментарий: получаем прогресс пользователя (массив) из usermeta
    $progress = get_user_meta( $user_id, 'lesson_progress', true );

    // Комментарий: если прогресс не массив — инициализируем пустой массив
    if ( ! is_array( $progress ) ) {
        $progress = array();
    }

    // Комментарий: если нет массива для данного продукта — инициализируем
    if ( ! isset( $progress[ $product_id ] ) || ! is_array( $progress[ $product_id ] ) ) {
        $progress[ $product_id ] = array();
    }

    // Комментарий: сохраняем временную метку с ключом lesson_id, значение — текущая метка времени (mysql формат)
    $progress[ $product_id ][ $lesson_id ] = current_time( 'mysql' );

    // Комментарий: обновляем usermeta с новым прогрессом
    update_user_meta( $user_id, 'lesson_progress', $progress );

    // Комментарий: корректно завершаем ajax-ответ
    wp_die( 'OK' );
}


// -----------------------
// 2) ВСПОМОГАТЕЛЬНАЯ ФУНКЦИЯ: возвращает объект с данными прогресса (user_id, product_id, has_access, product_progress)
// -----------------------

// Комментарий: функция, удобная для вызова из шаблонов — возвращает объект с информацией о прогрессе
function get_lesson_progress_for_current_user( $product_id = 0 ) {
    // Комментарий: получаем глобальный объект поста/продукта, если product_id не передали в аргументе
    if ( ! $product_id ) {
        // Комментарий: напрямую пытаемся взять глобальный $post
        global $post;
        // Комментарий: если глобальный пост есть — берём его ID
        if ( isset( $post ) && $post ) {
            $product_id = $post->ID;
        } else {
            // Комментарий: если product_id так и не определён — возвращаем null
            return null;
        }
    }

    // Комментарий: получаем ID текущего пользователя
    $user_id = get_current_user_id();

    // Комментарий: подготавливаем объект результата (stdClass)
    $result = new stdClass();

    // Комментарий: сохраняем user_id в объект
    $result->user_id = $user_id;

    // Комментарий: сохраняем product_id в объект
    $result->product_id = intval( $product_id );

    // Комментарий: читаем usermeta lesson_progress (может быть массив)
    $progress = get_user_meta( $user_id, 'lesson_progress', true );

    // Комментарий: если нет массива — инициализируем пустой массив
    if ( ! is_array( $progress ) ) {
        $progress = array();
    }

    // Комментарий: получаем прогресс для конкретного продукта (если есть) — это массив lesson_id => timestamp
    $product_progress = isset( $progress[ $product_id ] ) && is_array( $progress[ $product_id ] ) ? $progress[ $product_id ] : array();

    // Комментарий: сохраняем массив product_progress в объект
    $result->product_progress = $product_progress;

    // Комментарий: признак: есть ли вообще доступ/покупка курса (можно адаптировать под вашу логику)
    // Комментарий: проверяем: залогинен ли пользователь и купил ли он продукт (wc_customer_bought_product)
    $result->has_access = $user_id && wc_customer_bought_product( '', $user_id, $product_id );

    // Комментарий: возвращаем объект с полями user_id, product_id, product_progress (массив), has_access (bool)
    return $result;
}


Пояснение:

Если у тебя связь урок→продукт хранится под другим ключом, замени '_related_product' на свой.

В update_user_meta мы сохраняем массив, WP сериализует его автоматически.

Шаг 3 — Подключение и локализация JavaScript (в functions.php)

Нам нужен скрипт, который на странице урока поймает событие Play и вызовет AJAX. Зарегистрируем и локализуем скрипт (чтобы передать ajax_url и nonce).

<?php
// Комментарий: регистрируем и подключаем фронтенд-скрипт для уроков
add_action( 'wp_enqueue_scripts', 'enqueue_lesson_progress_script' );

function enqueue_lesson_progress_script() {
    // Комментарий: проверяем, что мы на singular('lesson') — чтобы подключать скрипт только на страницах уроков
    if ( ! is_singular( 'lesson' ) ) {
        // Комментарий: не подключаем скрипт если это не страница урока
        return;
    }

    // Комментарий: регистрируем файл скрипта; второй аргумент — путь до файла. Можно положить скрипт в тему в /js/lesson-progress.js
    wp_register_script(
        'lesson-progress',
        get_stylesheet_directory_uri() . '/js/lesson-progress.js', // путь к файлу
        array(), // зависимости (если нужны: ['jquery'])
        null,
        true // в footer
    );

    // Комментарий: генерируем nonce для безопасности AJAX-запросов
    $nonce = wp_create_nonce( 'mark_lesson_watched_nonce' );

    // Комментарий: локализуем параметры для скрипта — передаём AJAX URL, lesson id и nonce
    wp_localize_script( 'lesson-progress', 'LessonProgressData', array(
        // Комментарий: URL для AJAX-запросов в WP
        'ajax_url'  => admin_url( 'admin-ajax.php' ),
        // Комментарий: nonce для защиты от CSRF
        'nonce'     => $nonce,
        // Комментарий: ID урока — получаем текущий пост ID
        'lesson_id' => get_the_ID(),
    ) );

    // Комментарий: подключаем зарегистрированный скрипт
    wp_enqueue_script( 'lesson-progress' );
}


Где положить файл lesson-progress.js?

Создай файл wp-content/themes/тема/js/lesson-progress.js и помести туда JS из следующего шага.

Шаг 4 — Клиентский JS (js/lesson-progress.js)

Скрипт будет искать тег <video> и вешать обработчик play. Комментарии перед каждой строкой объясняют переменные и действия.

// Комментарий: ждём, пока DOM загрузится полностью
document.addEventListener('DOMContentLoaded', function () {

    // Комментарий: переменная video — мы ищем первый тег <video> на странице урока
    // Комментарий: если видео встроено как mediaelement/HTML5 — этот селектор сработает
    var video = document.querySelector('video');

    // Комментарий: если видео не найдено, ничего не делаем (выходим из функции)
    if (!video) {
        return;
    }

    // Комментарий: переменная marked — флаг, чтобы отправлять AJAX только один раз за сессию/страницу
    var marked = false;

    // Комментарий: вешаем слушатель на событие 'play' у HTMLVideoElement
    video.addEventListener('play', function () {
        // Комментарий: если уже отправили отметку — не отправляем снова
        if (marked) return;

        // Комментарий: помечаем как отправленное
        marked = true;

        // Комментарий: подготавливаем параметры запроса (lesson_id и nonce передали через wp_localize_script в PHP)
        var params = new URLSearchParams();
        // Комментарий: добавляем action для WP AJAX
        params.append('action', 'mark_lesson_watched');
        // Комментарий: добавляем lesson id, который был передан из PHP в LessonProgressData.lesson_id
        params.append('lesson_id', LessonProgressData.lesson_id);
        // Комментарий: добавляем nonce для безопасности
        params.append('nonce', LessonProgressData.nonce);

        // Комментарий: делаем GET запрос к admin-ajax.php
        fetch(LessonProgressData.ajax_url + '?' + params.toString(), {
            // Комментарий: используем credentials 'same-origin' если нужно отправлять куки (обычно по умолчанию fetch не отправляет)
            credentials: 'same-origin'
        })
        .then(function(response) {
            // Комментарий: можно посмотреть статус ответа
            // Комментарий: но для простоты — ничего не делаем
        })
        .catch(function(error) {
            // Комментарий: в консоле можно увидеть ошибку при отладке
            console.error('Error marking lesson watched:', error);
        });
    }, { once: false }); // Комментарий: можно оставить false, но у нас уже флаг marked предотвращает повтор
});


Примечание:

Если на странице несколько видео — можно модифицировать селектор и использовать data-атрибуты.

fetch использует GET-параметры (как в нашем серверном обработчике мы читали $_GET).

Шаг 5 — Вывод прогресса в списке уроков / sidebar (PHP)

Нужно изменить функцию, которая выводит список уроков в sidebar (или product loop), чтобы она подсвечивала просмотренные уроки. Ниже пример для функции, которая выводит sidebar — вставь туда, где у тебя был предыдущий код tg_show_course_sidebar.

<?php
// Комментарий: функция выводит боковую панель с уроками и подсветкой прогресса
function tg_show_course_sidebar( $product_id ) {
    // Комментарий: если нет product_id — прекращаем
    if ( ! $product_id ) return;

    // Комментарий: получаем список уроков (предполагается, что в метаполе '_product_lessons' хранится массив ID уроков)
    $lessons = get_post_meta( $product_id, '_product_lessons', true );

    // Комментарий: если уроков нет или это не массив — прекращаем
    if ( empty( $lessons ) || ! is_array( $lessons ) ) return;

    // Комментарий: получаем прогресс текущего пользователя для этого продукта
    $progress_obj = get_lesson_progress_for_current_user( $product_id );

    // Комментарий: если не получили объект — инициализируем пустой массив для удобства
    $product_progress = ( $progress_obj && isset( $progress_obj->product_progress ) ) ? $progress_obj->product_progress : array();

    // Комментарий: определяем id текущего урока, чтобы пометить 'current' (на странице lesson)
    $current_lesson_id = get_the_ID();

    // Комментарий: начинаем вывод HTML боковой панели
    echo '<aside class="course-sidebar">';
    echo '<h3>Все уроки курса</h3>';
    echo '<ul class="course-lessons">';

    // Комментарий: проходим по всем урокам и выводим ссылку с классами
    foreach ( $lessons as $lesson_id ) {
        // Комментарий: получаем объект урока (WP_Post)
        $lesson = get_post( $lesson_id );
        // Комментарий: если урока нет — пропускаем
        if ( ! $lesson ) continue;

        // Комментарий: класс active, если это текущая страница урока
        $active_class = ( $lesson_id == $current_lesson_id ) ? ' current' : '';

        // Комментарий: класс completed, если пользователь смотрел этот урок (есть запись в product_progress)
        $completed_class = isset( $product_progress[ $lesson_id ] ) ? ' completed' : '';

        // Комментарий: объединяем классы
        $classes = trim( $active_class . ' ' . $completed_class );

        // Комментарий: выводим элемент списка с нужными классами
        echo '<li class="lesson-item ' . esc_attr( $classes ) . '">';
        echo '<a href="' . esc_url( get_permalink( $lesson_id ) ) . '">' . esc_html( $lesson->post_title ) . '</a>';
        echo '</li>';
    }

    // Комментарий: закрываем список и блок
    echo '</ul>';
    echo '</aside>';
}


Как использовать:

В месте, где выводился твой sidebar, вызывай tg_show_course_sidebar( $product_id ); (где $product_id — ID товара/курса).

Шаг 6 — Показ «последнего урока, на котором остановились» (в карточке товара)

Добавим функцию, которая выводит ссылку на последний просмотренный урок в карточке товара (single product или loop). Комментарии перед каждой строчкой — как просил.

<?php
// Комментарий: выводит блок "Вы остановились на уроке X" для данного product_id
function tg_show_last_stopped_lesson( $product_id ) {
    // Комментарий: получаем объект прогресса для текущего пользователя и product
    $progress_obj = get_lesson_progress_for_current_user( $product_id );

    // Комментарий: если нет прогресса или пользователь не залогинен — ничего не выводим
    if ( ! $progress_obj || empty( $progress_obj->product_progress ) ) {
        return;
    }

    // Комментарий: берём массив lesson_id => timestamp
    $product_progress = $progress_obj->product_progress;

    // Комментарий: получаем последние просмотренные ID — keys массива
    $ids = array_keys( $product_progress );

    // Комментарий: если ids пуст — ничего не выводим
    if ( empty( $ids ) ) return;

    // Комментарий: последний просмотренный урок — последний элемент массива (используем end)
    $last_lesson_id = end( $ids );

    // Комментарий: получаем ссылку и название урока
    $lesson_link = get_permalink( $last_lesson_id );
    $lesson_title = get_the_title( $last_lesson_id );

    // Комментарий: выводим HTML-блок с ссылкой "Продолжить"
    echo '<div class="last-lesson-block">';
    echo 'Вы остановились на уроке: <a href="' . esc_url( $lesson_link ) . '">' . esc_html( $lesson_title ) . '</a>';
    echo '</div>';
}


Где вызвать:

В шаблоне product (например, в woocommerce_single_product_summary через add_action) — вызови tg_show_last_stopped_lesson( $product_id ).

Шаг 7 — Стили (CSS)

Добавь в свою тему (например, style.css) следующие стили, чтобы увидеть подсветку:

/* Комментарий: стиль для просмотренных уроков — изменяет цвет и делает чуть прозрачным */
.lesson-item.completed a {
    color: #0ca80c;
    opacity: 0.8;
}

/* Комментарий: стиль для текущего урока — делает текст жирным и подчёркнутым */
.lesson-item.current a {
    font-weight: 700;
    text-decoration: underline;
}

/* Комментарий: стиль для блока "Вы остановились на уроке" */
.last-lesson-block {
    margin: 10px 0;
    padding: 10px;
    background: #f6f9f6;
    border-left: 4px solid #0ca80c;
}

Где вставлять что (коротко)

functions.php темы: вставь весь PHP код из шагов 2 и 3.

js/lesson-progress.js: вставь JS из шага 4.

Sidebar/template: используй функцию tg_show_course_sidebar($product_id) из шага 5.

В карточке товара (где хочешь показать «продолжить»): вызови tg_show_last_stopped_lesson($product_id).

CSS: добавь в style.css.

Мини-FAQ / Подсказки

Если твои уроки связаны с продуктом другим способом (не _related_product), сообщи точный ключ метаполя — я исправлю код.

Если используешь кастомную систему шаблонов — вместо is_singular('lesson') адаптируй условие.

Если видео не HTML5 (YouTube iframe) — для YouTube нужен другой обработчик (YouTube Iframe API). Я могу дать скрипт для YouTube/Vimeo по запросу.

Для безопасности мы используем nonce — не убирай этот механизм.

Для отладки: временно в AJAX-обработчике делай error_log( print_r($progress, true) ); и смотри лог, если что-то не работает.

Хочешь — я:

а) Преобразую всё в готовый патч (один файл functions.php + lesson-progress.js) и пришлю тебе, или

б) Подправлю код под твои реальные метаполя (скажи точный ключ связи урок→курс), или

в) Реализую версию для YouTube/Vimeo.

Что делаем дальше?