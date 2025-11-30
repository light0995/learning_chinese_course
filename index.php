<?php get_header(); ?>

<div class="container">
    <?php
    if (have_posts()) : // если есть посты
        while (have_posts()) : the_post(); // то инициализируем каждый пост по порядку
            // выполняем код для каждого конкретного поста
            the_title(); // например выводим заголовок
            the_content();
        endwhile;
    else:
        echo 'В этой категории нет записей, вероятно.';
    endif;
    ?>
</div>

<?php get_footer(); ?>
