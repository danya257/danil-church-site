<?php
/**
 * Church Theme Functions
 * Современная тема для православного храма
 */

function church_theme_setup() {
    // Регистрация меню
    register_nav_menus(array(
        'primary' => 'Главное меню',
        'footer'  => 'Меню в подвале',
        'mobile'  => 'Мобильное меню'
    ));
    
    // Поддержка возможностей темы
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'church_theme_setup');

function church_theme_scripts() {
    // Подключаем Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap', array(), null);
    
    // Основные стили
    wp_enqueue_style('main-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // jQuery
    wp_enqueue_script('jquery');
    
    // Основной JS
    wp_enqueue_script('church-theme-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'church_theme_scripts');

// Добавляем классы для анимации при скролле
function church_add_scroll_reveal_class($classes) {
    if (is_front_page()) {
        $classes[] = 'scroll-reveal-enabled';
    }
    return $classes;
}
add_filter('body_class', 'church_add_scroll_reveal_class');

// Улучшаем вывод excerpt
function church_custom_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'church_custom_excerpt_length', 999);

// Добавляем поддержку виджетов
function church_widgets_init() {
    register_sidebar(array(
        'name'          => 'Боковая панель',
        'id'            => 'sidebar-1',
        'description'   => 'Виджеты для боковой панели',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => 'Подвал 1',
        'id'            => 'footer-1',
        'description'   => 'Первая колонка подвала',
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'church_widgets_init');
?>