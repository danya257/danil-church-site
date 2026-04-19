<?php
/**
 * Template Name: Главная страница
 */
get_header(); 
?>

<!-- ===== СЛАЙДЕР (КАРУСЕЛЬ) ===== -->
<?php echo do_shortcode('[church_home_slider]'); ?>

<!-- ===== HERO SECTION (альтернатива, если слайдер пустой) ===== -->
<?php 
$slider_settings = church_get_slider_settings();
if (empty($slider_settings['slides'])): 
?>
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title gold-shine scroll-reveal from-bottom">
            Собор Александра Невского
        </h1>
        <p class="hero-subtitle scroll-reveal from-bottom delay-1">
            Добро пожаловать в наш храм. Место молитвы, единства и духовного роста.
        </p>
        <div class="hero-buttons scroll-reveal from-bottom delay-2">
            <a href="<?php echo esc_url(home_url('/schedule')); ?>" class="hero-button pulse">
                Расписание богослужений
            </a>
            <a href="<?php echo esc_url(home_url('/donate')); ?>" class="hero-button-outline">
                Поддержать храм
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== О ХРАМЕ ===== -->
<section class="about-church-main" style="padding: 80px 0; background: white;">
    <div class="container">
        <div class="about-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            
            <div class="about-media scroll-reveal from-left">
                <?php 
                $church_image = get_theme_mod('church_main_image');
                if ($church_image): ?>
                    <img src="<?php echo esc_url($church_image); ?>" 
                         alt="Войсковой Собор Александра Невского" 
                         style="width: 100%; border-radius: 12px; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
                <?php else: ?>
                    <div style="width: 100%; height: 450px; background: linear-gradient(135deg, var(--color-primary), #2d2d44); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 64px;">
                        ⛪
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="about-content scroll-reveal from-right delay-1">
                <h2 style="font-size: 36px; margin-bottom: 25px; font-family: 'Playfair Display', serif; color: var(--color-primary);">
                    О нашем храме
                </h2>
                <div class="ornament-divider"></div>
                
                <?php 
                $description = get_theme_mod('church_description');
                if ($description): ?>
                    <div style="line-height: 1.8; color: var(--color-text); font-size: 16px;">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php else: ?>
                    <p style="line-height: 1.8; color: var(--color-text); font-size: 16px; margin-bottom: 20px;">
                        Войсковой Собор Александра Невского — это духовный центр нашего города, 
                        место где верующие собираются для совместной молитвы, участия в таинствах 
                        и духовного роста.
                    </p>
                    <p style="line-height: 1.8; color: var(--color-text); font-size: 16px; margin-bottom: 20px;">
                        Наш храм открыт для всех, кто ищет Бога, утешения и общения с единомышленниками. 
                        Мы приглашаем вас принять участие в богослужениях, воскресной школе и социальных служениях.
                    </p>
                <?php endif; ?>
                
                <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(home_url('/history')); ?>" class="hero-button">
                        История храма
                    </a>
                    <a href="<?php echo esc_url(home_url('/clergy')); ?>" class="hero-button-outline" style="padding: 14px 30px; border-radius: 50px; border: 2px solid var(--color-primary); color: var(--color-primary);">
                        Духовенство
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- ===== БЫСТРЫЕ ССЫЛКИ ===== -->
<section class="quick-links-section" style="padding: 80px 0; background: white;">
    <div class="container">
        <div class="section-title scroll-reveal scale-in" style="text-align: center; margin-bottom: 50px;">
            <h2>Разделы сайта</h2>
            <p style="color: var(--color-text-light); max-width: 600px; margin: 15px auto 0;">
                Выберите интересующий вас раздел для получения подробной информации
            </p>
            <div class="ornament-divider"></div>
        </div>
        
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <a href="<?php echo esc_url(home_url('/schedule')); ?>" class="feature-card scroll-reveal from-left" style="text-decoration: none;">
                <div class="service-icon">🕊️</div>
                <h4 style="margin: 15px 0 10px;">Богослужения</h4>
                <p style="color: var(--color-text-light); font-size: 14px;">Расписание служб</p>
            </a>
            <a href="<?php echo esc_url(home_url('/services')); ?>" class="feature-card scroll-reveal from-bottom delay-1" style="text-decoration: none;">
                <div class="service-icon">✝️</div>
                <h4 style="margin: 15px 0 10px;">Таинства</h4>
                <p style="color: var(--color-text-light); font-size: 14px;">Крещение, венчание</p>
            </a>
            <a href="<?php echo esc_url(home_url('/donate')); ?>" class="feature-card scroll-reveal from-bottom delay-2" style="text-decoration: none;">
                <div class="service-icon">💛</div>
                <h4 style="margin: 15px 0 10px;">Пожертвовать</h4>
                <p style="color: var(--color-text-light); font-size: 14px;">Поддержать храм</p>
            </a>
            <a href="<?php echo esc_url(home_url('/news')); ?>" class="feature-card scroll-reveal from-right delay-3" style="text-decoration: none;">
                <div class="service-icon">📰</div>
                <h4 style="margin: 15px 0 10px;">Новости</h4>
                <p style="color: var(--color-text-light); font-size: 14px;">События прихода</p>
            </a>
        </div>
    </div>
</section>

<!-- ===== ПРИГЛАШЕНИЕ (ЗОЛОТЫЕ ЗАГОЛОВКИ) ===== -->
<section class="cta-section">
    <div class="container">
        <h2 class="scroll-reveal scale-in">
            Приходите в наш храм
        </h2>
        <p class="scroll-reveal from-bottom delay-1">
            Каждого прихожанина и гостя мы рады видеть на наших богослужениях. 
            Присоединяйтесь к молитве и духовному общению.
        </p>
        <a href="<?php echo esc_url(home_url('/contacts')); ?>" class="hero-button scroll-reveal from-bottom delay-2">
            Как нас найти
        </a>
    </div>
</section>

<?php get_footer(); ?>