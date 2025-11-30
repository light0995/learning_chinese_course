<?php

// правильный способ подключить стили и скрипты
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts() {
  wp_enqueue_style( 'style-name', get_stylesheet_uri() );
  wp_enqueue_style( 'style-name-my.css', get_template_directory_uri() . "/assets/css/style.css");
  wp_enqueue_style('my-media.css', get_template_directory_uri()."/assets/css/media.css");

  wp_enqueue_script( 'script-1', get_template_directory_uri() . '/assets/js/script.js', '1.0.0', true );
  // wp_enqueue_script( 'script-2', get_template_directory_uri() . '/assets/js/script2.js', array(), '1.0.0', true );
}

add_action(
    'after_setup_theme',
    function () {

        add_theme_support('post-thumbnails');

        add_theme_support('custom-logo', [
            'height'      => 80,
            'width'       => 80,
        ]);
        add_theme_support('html5', array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'script',
            'style',
        ));
        
        add_theme_support('menus');
        // Подключение областей меню

        register_nav_menus([
            'header-menu' => 'Меню в шапке сайта',
            'footer-menu' => 'Меню в подвале сайта',
            'footer-nav' => 'Меню в подвале навигация',
        ]);


        add_theme_support('title-tag');
    }
);


add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);

function special_nav_class($classes, $item){
    $classes[] = 'header__menu-item';
    return $classes;
}

add_action('init', 'create_courses_post_type');

function create_courses_post_type()
{
    register_post_type('course', [
        'labels' => [
            'name' => 'Курсы',
            'singular_name' => 'Курс',
            'add_new' => 'Добавить курс',
            'add_new_item' => 'Добавить новый курс',
            'edit_item' => 'Рёактировать курс',
            'new_item' => 'Новый курс',
            'view_item' => 'Посмотреть курс',
            'search_items' => 'Поиск курсов',
            'not_found' => 'Курсы не найдены',
            'menu_name' => 'Курсы языков'
        ],
        'public' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'courses'],
        'show_in_rest' => true, // поддержка Gutenberg
    ]);
}



add_action('init', 'create_course_taxonomies');

function create_course_taxonomies()
{
    register_taxonomy('language', 'course', [
        'labels' => [
            'name' => 'Языки',
            'singular_name' => 'Язык',
            'search_items' => 'Поиск языков',
            'all_items' => 'Все языки',
            'edit_item' => 'Редактировать язык',
            'add_new_item' => 'Добавить язык'
        ],
        'hierarchical' => true, // делает её похожей на категории
        'show_in_rest' => true, // поддержка Gutenberg
    ]);
}

remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);



add_action( 'init', 'register_post_types' );

function register_post_types(){

	register_post_type( 'lesson', [
		'label'  => null,
		'labels' => [
			'name'               => 'Уроки', // основное название для типа записи
			'singular_name'      => 'Урок', // название для одной записи этого типа
			'add_new'            => 'Добавить ', // для добавления новой записи
			'add_new_item'       => 'Добавить урок', // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => 'Редактировать урок', // для редактирования типа записи
			'new_item'           => 'Новое ____', // текст новой записи
			'view_item'          => 'Смотреть ____', // для просмотра записи этого типа.
			'search_items'       => 'Искать ____', // для поиска по этим типам записи
			'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
			'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
			'parent_item_colon'  => '', // для родителей (у древовидных типов)
			// 'menu_name'          => '____', // название меню
		],
		'description'            => '',
		'public'                 => true,
        'show_in_rest' => true,
		'show_in_menu'           => null, // показывать ли в меню админки
		// 'show_in_admin_bar'   => null, // зависит от show_in_menu
		'rest_base'           => null, // $post_type. C WP 4.7
		'menu_position'       => null,
		//'capability_type'   => 'post',
		//'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
		//'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor', 'thumbnail'], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'          => [],
		'has_archive'         => false,
		'rewrite'             => true,
		'query_var'           => true,
	] );

}



// === Метабокс "Уроки курса" в товарах ===
function tg_add_lessons_to_product_metabox()
{
    add_meta_box(
        'product_lessons_box',
        'Уроки курса',
        'tg_product_lessons_metabox_callback',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'tg_add_lessons_to_product_metabox');



function tg_product_lessons_metabox_callback($post)
{
    $selected_lessons = get_post_meta($post->ID, '_product_lessons', true);
    if (!is_array($selected_lessons)) $selected_lessons = [];

    // Получаем все уроки
    $lessons = get_posts([
        'post_type' => 'lesson',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    if (empty($lessons)) {
        echo '<p>Пока нет доступных уроков.</p>';
        return;
    }

    echo '<p>Выберите уроки, которые входят в этот курс:</p>';
    echo '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 6px;">';
    foreach ($lessons as $lesson) {
        $checked = in_array($lesson->ID, $selected_lessons) ? 'checked' : '';
        echo '<label style="display:block; margin-bottom:4px;">';
        echo '<input type="checkbox" name="product_lessons[]" value="' . esc_attr($lesson->ID) . '" ' . $checked . '> ';
        echo esc_html($lesson->post_title);
        echo '</label>';
    }
    echo '</div>';
}




function tg_save_product_lessons_meta($post_id)
{
    if (isset($_POST['product_lessons'])) {
        $lesson_ids = array_map('intval', $_POST['product_lessons']);
        update_post_meta($post_id, '_product_lessons', $lesson_ids);
    } else {
        delete_post_meta($post_id, '_product_lessons');
    }
}
add_action('save_post_product', 'tg_save_product_lessons_meta');


// === Вывод уроков на странице курса (товара) с замочками ===
function tg_show_product_lessons()
{
    global $product;
    if (!$product) return;

    $product_id = $product->get_id();
    $lessons = get_post_meta($product_id, '_product_lessons', true);
    if (empty($lessons) || !is_array($lessons)) return;

    $user_id = get_current_user_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);
    // if (current_user_can("administrator")) {
    //     $has_access = true;
    // }
    echo '<div class="product-lessons">';
    echo '<h3>Уроки курса</h3>';
    echo '<ul class="lesson-list">';

    foreach ($lessons as $lesson_id) {
        $lesson = get_post($lesson_id);
        if (!$lesson) continue;

        $title = esc_html($lesson->post_title);


        if ($has_access) {
            // ✅ Покупатель — открываем ссылки
            echo '<li class="lesson-item"><a href="' . get_permalink($lesson_id) . '">' . $title . '</a></li>';
        } else {
            // 🔒 Без покупки — просто текст и замочек
            echo '<li class="lesson-item locked">' . $title . ' <span class="lock">🔒</span></li>';
        }
    }

    echo '</ul>';

    if (!$has_access) {
        echo '<div class="buy-access">';
        echo '<p>Чтобы получить доступ к урокам, купите этот курс</p>';
        //echo '<a href="' . esc_url(get_permalink($product_id)) . '" class="button">Купить курс</a>';
        echo '</div>';
    }

    echo '</div>';
}







// Тут буду настройки шаблонов WooCommerce
require get_template_directory() . '/inc/woo-function/woo-templates.php';
















