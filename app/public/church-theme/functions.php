<?php
function church_theme_setup() {
    // Регистрация меню
    register_nav_menus(array(
        'primary' => 'Главное меню',
        'footer'  => 'Меню в подвале'
    ));
    
    // Поддержка миниатюр и заголовков
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'church_theme_setup');

function church_theme_scripts() {
    // Подключаем стили
    wp_enqueue_style('main-style', get_stylesheet_uri());
    
    // Подключаем скрипты
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'church_theme_scripts');
?>