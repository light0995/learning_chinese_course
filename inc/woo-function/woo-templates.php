<?php
add_action('woocommerce_after_single_product_summary', 'tg_show_product_lessons', 15);




// === Сайдбар с уроками курса ===
function tg_show_course_sidebar($product_id)
{
    echo $product_id;
    if (!$product_id) return;

    $lessons = get_post_meta($product_id, '_product_lessons', true);
    if (empty($lessons) || !is_array($lessons)) return;

    echo '<aside class="course-sidebar">';
    echo '<h3>Все уроки курса</h3>';
    echo '<ul class="course-lessons">';

    $current_lesson_id = get_the_ID();

    foreach ($lessons as $lesson_id) {
        $lesson = get_post($lesson_id);
        if (!$lesson) continue;

        $active_class = ($lesson_id == $current_lesson_id) ? ' class="active"' : '';

        echo '<li' . $active_class . '>';
        echo '<a href="' . get_permalink($lesson_id) . '">' . esc_html($lesson->post_title) . '</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</aside>';
}

add_action('woocommerce_after_single_product_summary', 'tg_show_course_sidebar', 20);


add_filter( 'woocommerce_account_menu_items', function( $items ) {
    unset( $items['downloads'] );
    return $items;
});





// функции из юкомсерс ненужные отключить. Сделать проверку,если текущий пользователь купил курс и авторизован, выполнить такие то действия, аесли нет 
// то заново вызвать функцию цены и кнопку покупки

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_show_access_in_loop', 9);







function woocommerce_show_access_in_loop () {
    // echo "Info purchased course";
    global $product;
    $product_id = get_the_ID();
    // === Проверка доступа ===
    $product = wc_get_product($product_id);
    $user_id = get_current_user_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

    if ($user_id && $has_access) {


        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        echo '<h3>У вас есть доступ к этому курсу ✅</h3>';
        

    }
};




add_action('woocommerce_before_shop_loop_item', 'ak_woocommerce_before_shop_loop_item', 5);

function ak_woocommerce_before_shop_loop_item()
{
    
    // echo "Info purchased course";
    global $product;
    $product_id = get_the_ID();
    // === Проверка доступа ===
    $product = wc_get_product($product_id);
    $user_id = get_current_user_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

    if ($user_id && $has_access) {

        
        $lessons = get_post_meta($product_id, '_product_lessons', true);
        if (empty($lessons) || !is_array($lessons)) return;

        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    
        $current_lesson_id = get_the_ID();
    
        foreach ($lessons as $lesson_id) {
            $lesson = get_post($lesson_id);
            if (!$lesson) continue;
    
            echo '<a href="' . get_permalink($lesson_id) . '"> ';
            return;

        }
    

    }

}



function check_product_access () {
    global $product;
    if (!$product) return;

    $user_id = get_current_user_id();
    $product_id = $product->get_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

    return [
        'user_id' => $user_id,
        'product_id' => $product_id,
        'has_access' => $has_access
    ];
};






// 1️⃣ Показываем уведомление вне summary
add_action('woocommerce_before_single_product', 'my_course_access_notice', 5);
function my_course_access_notice() {
    global $product;
    if (!$product) return;

    $user_id = get_current_user_id();
    $product_id = $product->get_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

    if ($has_access) {
        echo '<div class="single_product_wrapper">';
        echo '<h3 class="single_product_notice">✅ У вас есть доступ к этому курсу!</h3>';
        echo '</div>';

    }
}

// 2️⃣ Скрываем стандартные элементы внутри summary
add_action('woocommerce_single_product_summary', 'my_course_hide_price_cart_meta_excerpt', 1);
function my_course_hide_price_cart_meta_excerpt() {
    global $product;
    if (!$product) return;

    $user_id = get_current_user_id();
    $product_id = $product->get_id();
    $has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

    if ($has_access) {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    }
}

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );




//Обертка для магазина
add_action('woocommerce_before_shop_loop', 'my_test_function', 40);


function my_test_function () {

    ?>

    <div class="my_test">Где я 


    <?php
}


//Закрывающая обертка для магазина

add_action('woocommerce_after_shop_loop', 'my_test_function_2', 20);


function my_test_function_2 () {

    ?>
    Закрываюсь
    </div>
    <?php
}