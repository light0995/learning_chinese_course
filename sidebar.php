

<?php 



if (is_product()) {
    
    // global $product;
    // $product_id = $product->get_id();

    $product_id = get_the_ID();

    echo "<pre>"; 
    
    // print_r($product);
    
   echo "</pre>";

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
};

