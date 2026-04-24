<?php
/**
 * Header template - Центрированная шапка с золотым названием
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">Перейти к содержимому</a>

<!-- ===== ШАПКА САЙТА - ЦЕНТРИРОВАННАЯ ===== -->
<header class="site-header-centered" id="site-header">
    <div class="container">
        
        <!-- Название храма - золотое, по центру -->
        <div class="site-title-centered">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                ВОЙСКОВОЙ СОБОР АЛЕКСАНДРА НЕВСКОГО
            </a>
        </div>

        <!-- Навигация - под названием, по центру -->
        <nav class="main-navigation-centered">
            <?php
            wp_nav_menu(array(
                'theme_location'  => 'primary',
                'menu_class'      => 'menu-primary-centered',
                'container'       => false,
                'fallback_cb'     => false,
                'depth'           => 2,
            ));
            ?>
        </nav>

        <!-- Кнопка мобильного меню -->
        <button class="mobile-toggle" aria-label="Открыть меню">
            <span class="toggle-icon"></span>
        </button>

    </div>
</header>

<!-- ===== МОБИЛЬНОЕ МЕНЮ ===== -->
<div class="mobile-menu-overlay" id="mobile-overlay">
    <div class="mobile-menu-container">
        <button class="mobile-menu-close">&times;</button>
        <nav class="mobile-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-menu-list',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
        </nav>
    </div>
</div>

<main id="content" class="site-content">
