
<?php

/* Можем не использовать шаблон page совсем, по этому я его преименовал на page2 */
get_header(); ?>

       <?php
        if (have_posts()) :
                while (have_posts()) : the_post();
                        the_title();
                        the_content(); ?>
    <?php
                endwhile;
        else :

                echo "контента нет";
        endif;
        ?>

<?php get_footer(); ?>
