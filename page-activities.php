<?php
/**
 * Template Name: Деятельность
 */
get_header(); 
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: beige; margin-bottom: 15px; font-family: 'Playfair Display', serif;"><?php the_title(); ?></h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Приходская жизнь и служения</p>
    </div>
</section>

<section class="activities-full" style="padding: 80px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 1100px;">
        <div class="activities-content scroll-reveal" style="background: white; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <?php 
            while (have_posts()): the_post();
                the_content();
            endwhile; 
            ?>
            
            <!-- Сетка деятельности -->
            <div class="services-grid" style="margin-top: 50px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <a href="<?php echo esc_url(home_url('/bible-group')); ?>" class="service-card scroll-reveal" style="text-decoration: none;">
                    <div class="service-icon">📖</div>
                    <h4>Библейская группа</h4>
                    <p style="color: var(--color-text-light);">Изучение Священного Писания</p>
                </a>
                <a href="<?php echo esc_url(home_url('/sunday-school')); ?>" class="service-card scroll-reveal delay-1" style="text-decoration: none;">
                    <div class="service-icon">🎓</div>
                    <h4>Воскресная школа</h4>
                    <p style="color: var(--color-text-light);">Обучение для взрослых</p>
                </a>
                <a href="<?php echo esc_url(home_url('/youth')); ?>" class="service-card scroll-reveal delay-2" style="text-decoration: none;">
                    <div class="service-icon">👫</div>
                    <h4>Молодёжное объединение</h4>
                    <p style="color: var(--color-text-light);">Встречи и мероприятия</p>
                </a>
                <a href="<?php echo esc_url(home_url('/cossacks')); ?>" class="service-card scroll-reveal" style="text-decoration: none;">
                    <div class="service-icon">⚔️</div>
                    <h4>Казачество</h4>
                    <p style="color: var(--color-text-light);">Казачье служение</p>
                </a>
                <a href="<?php echo esc_url(home_url('/sisterhood')); ?>" class="service-card scroll-reveal delay-1" style="text-decoration: none;">
                    <div class="service-icon">🙏</div>
                    <h4>Сестринство</h4>
                    <p style="color: var(--color-text-light);">Женское служение</p>
                </a>
                <a href="<?php echo esc_url(home_url('/choir')); ?>" class="service-card scroll-reveal delay-2" style="text-decoration: none;">
                    <div class="service-icon">🎵</div>
                    <h4>Хор</h4>
                    <p style="color: var(--color-text-light);">Церковное пение</p>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>