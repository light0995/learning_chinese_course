
<?php
get_header();

if (!is_singular('lesson')) return;

$lesson_id = get_the_ID();

// === Ищем товар, к которому привязан этот урок ===
$products = get_posts([
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'meta_key'       => '_product_lessons',
]);

// echo '<pre>';
// print_r($products);
// echo '</pre>';

$product_id = null;

// Перебираем все товары и ищем, где этот урок есть в массиве
foreach ($products as $product_post) {
    $lessons = get_post_meta($product_post->ID, '_product_lessons', true);
    // if (is_array($lessons) && in_array($lesson_id, $lessons)) {
        $product_id = $product_post->ID;
        break;
    // }
}

if (!$product_id) {
    echo '<div class="container"><p>❌ Урок не привязан ни к какому курсу (товару).</p></div>';
    get_footer();
    return;
}

// === Проверка доступа ===
$product = wc_get_product($product_id);
$user_id = get_current_user_id();
$has_access = $user_id && wc_customer_bought_product('', $user_id, $product_id);

?>
<div class="container">
    <div class="lesson-layout">

        <div class="lesson-content">
            <?php
            // 🚫 Не авторизован
            if (current_user_can('administrator')) {
                $has_access = true;
            }
            
            if (!$user_id) {
                echo '
                    <div class="lesson-locked">
                        <p>🔒 Этот урок доступен только авторизованным пользователям.</p>
                        <a href="' . wp_login_url(get_permalink()) . '" class="button">Войти</a>
                    </div>
                ';
            } elseif (!$has_access) {
                $price = $product ? $product->get_price_html() : '';
                $buy_url = get_permalink($product_id);
                echo '
                    <div class="lesson-locked">
                        <p>❌ Этот урок доступен только после покупки курса.</p>
                        <p><strong>Цена: ' . $price . '</strong></p>
                        <a href="' . esc_url($buy_url) . '" class="button">Купить курс</a>
                    </div>
                ';
            } else {
                while (have_posts()) : the_post(); ?>
                    <h1><?php the_title(); ?></h1>
                    <div class="lesson-text"><?php the_content(); ?></div>
            <?php endwhile;
            }
            ?>
        </div>

        <div class="lesson-sidebar-wrapper">
            <?php tg_show_course_sidebar($product_id); ?>
        </div>

    </div>
</div>

<?php get_footer(); ?>