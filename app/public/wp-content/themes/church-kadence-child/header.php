<?php
/**
 * Header template for Church Kadence Child
 * Исправленная версия - без дублей и ошибок
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

<!-- ===== VIDEO INTRO OVERLAY (ТОЛЬКО ГЛАВНАЯ СТРАНИЦА) ===== -->
<?php if (is_front_page()): ?>
<div class="video-intro-overlay" id="videoIntro">
    <div class="video-intro-container">
        <video class="video-intro" id="introVideo" autoplay muted playsinline preload="auto">
            <source src="<?php echo get_stylesheet_directory_uri(); ?>/assets/videos/church-intro.mp4" type="video/mp4">
            Ваш браузер не поддерживает видео.
        </video>
        <div class="video-intro-logo">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <h1><?php bloginfo('name'); ?></h1>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== SKIP LINK (ACCESSIBILITY) ===== -->
<a class="skip-link screen-reader-text" href="#content">
    <?php esc_html_e('Перейти к содержимому', 'church-kadence-child'); ?>
</a>

<!-- ===== ШАПКА САЙТА ===== -->
<header class="site-header" id="site-header">
    <div class="container header-inner">
        
        <!-- Логотип -->
        <div class="site-logo">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-text">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Кнопка мобильного меню -->
        <button class="mobile-toggle" aria-label="Открыть меню" aria-expanded="false" aria-controls="mobile-overlay">
            <span class="toggle-icon"></span>
        </button>
        
        <!-- Основное меню (Десктоп) -->
        <nav class="main-navigation" id="primary-menu">
            <?php
            wp_nav_menu(array(
                'theme_location'  => 'primary',
                'menu_class'      => 'menu-primary',
                'menu_id'         => 'primary-menu-list',
                'container'       => false,
                'fallback_cb'     => false,
                'depth'           => 3,
            ));
            ?>
        </nav>
        
    </div>
</header>

<!-- ===== МОБИЛЬНОЕ МЕНЮ (ОВЕРЛЕЙ) - ЕДИНСТВЕННЫЙ ЭКЗЕМПЛЯР ===== -->
<div class="mobile-menu-overlay" id="mobile-overlay">
    <div class="mobile-menu-container">
        <button class="mobile-menu-close" aria-label="Закрыть меню">&times;</button>
        
        <nav class="mobile-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-menu-list',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 3,
            ));
            ?>
        </nav>
        
        <!-- Кнопка пожертвований в мобильном меню -->
        <?php
        $donate = church_get_donate_settings();
        if ($donate['show'] && !empty($donate['url'])):
        ?>
        <div class="mobile-donate">
            <a href="<?php echo esc_url($donate['url']); ?>" class="donate-button">
                <?php echo esc_html($donate['text']); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== ОСНОВНОЙ КОНТЕНТ ===== -->
<main id="content" class="site-content">