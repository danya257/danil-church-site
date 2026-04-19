<?php
/**
 * Main template file
 */

get_header();
?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="scroll-reveal">Войсковой собор святого благоверного князя Александра Невского г. Краснодара — Приходской сайт Войскового собора г. Краснодара</h1>
            <p class="scroll-reveal"></p>
            <a href="#schedule" class="btn scroll-reveal">Расписание богослужений</a>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="section schedule-section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Расписание богослужений</h2>
            <?php if (has_post_thumbnail()) : ?>
                <div class="scroll-reveal">
                    <?php the_post_thumbnail('large', array('class' => 'schedule-img')); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Priests Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Духовенство собора</h2>
            <div class="priests-grid">
                <?php
                $priests = new WP_Query(array(
                    'post_type' => 'priest',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                ));
                
                if ($priests->have_posts()) :
                    while ($priests->have_posts()) : $priests->the_post();
                        ?>
                        <div class="priest-card scroll-reveal">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" class="priest-photo">
                            <?php endif; ?>
                            <div class="priest-info">
                                <h3 class="priest-name"><?php the_title(); ?></h3>
                                <p class="priest-position"><?php echo get_post_meta(get_the_ID(), '_priest_position', true); ?></p>
                                <p><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <h2 class="section-title scroll-reveal">История храма</h2>
            <div class="scroll-reveal" style="max-width: 800px; margin: 0 auto;">
                <p>...</p>
                <a href="/history" class="btn">Читать далее</a>
            </div>
        </div>
    </section>

    <!-- Contacts Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Контакты</h2>
            <div class="contacts-grid">
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">📍</div>
                    <h3>Адрес</h3>
                    <p>г. Санкт-Петербург, площадь Александра Невского, 1</p>
                </div>
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">📞</div>
                    <h3>Телефон</h3>
                    <p>+7 (812) 274-11-97</p>
                </div>
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">✉️</div>
                    <h3>Email</h3>
                    <p>info@alexfond.ru</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
