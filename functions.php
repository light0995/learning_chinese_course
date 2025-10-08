<?php

// правильный способ подключить стили и скрипты
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts() {
  wp_enqueue_style( 'style-name', get_stylesheet_uri() );
  wp_enqueue_style( 'style-name-my.css', get_template_directory_uri() . "/assets/css/style.css");

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
            'edit_item' => 'Редактировать курс',
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