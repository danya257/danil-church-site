<?php
/**
 * Church Theme Functions
 */

if (!defined('ABSPATH')) exit;

// Register menus
function church_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'church-theme'),
        'footer' => __('Footer Menu', 'church-theme'),
        'mobile' => __('Mobile Menu', 'church-theme'),
    ));
}
add_action('after_setup_theme', 'church_register_menus');

// Theme setup
function church_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    set_post_thumbnail_size(400, 300, true);
}
add_action('after_setup_theme', 'church_setup');

// Enqueue scripts and styles
function church_scripts() {
    wp_enqueue_style('church-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('church-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'church_scripts');

// Register widgets
function church_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'church-theme'),
        'id' => 'sidebar-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'church_widgets_init');

// Custom excerpt length
function church_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'church_excerpt_length');
