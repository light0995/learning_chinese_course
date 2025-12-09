<?php get_header(); ?>

<section class="contacts">
    <div class="container">
        <h2 class="contacts__title"><?php the_field('contacts_title'); ?></h2>

        <div class="contacts__content">
            <div class="contacts__content-left">
            <div class="contacts__list-title"><?php the_field('contacts_list-title') ?></div>
            <ul class="contacts__list-items">
                <li class="contacts__list-item"><?php the_field('contacts_telegram'); ?></li>
                <li class="contacts__list-item"><?php the_field('contacts_whatsapp'); ?></li>
                <li class="contacts__list-item"><?php the_field('contacts_email'); ?></li>
            </ul>
            <div class="contacts__subtitle"><?php the_field('contacts_subtitle') ?> </div>
            </div>
        

        <div class="contacts__content-right">
            <?php if ($form = get_field('contacts_form')): ?>

            <div class="contacts__form">
            <?php echo do_shortcode($form); ?>
            </div>

            <?php endif; ?>
            </div>
            
        </div>





    </div>
</section>



<?php get_footer(); ?>