<?php
/**
 * Template Name: История храма
 * Страница архива истории
 */
get_header(); 
?>

<section class="page-header history-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 100px 0; text-align: center; position: relative; overflow: hidden;">
    <!-- Декоративные элементы -->
    <div style="position: absolute; top: 20%; left: 10%; font-size: 48px; opacity: 0.1;">✦</div>
    <div style="position: absolute; bottom: 15%; right: 15%; font-size: 64px; opacity: 0.1;">📜</div>
    
    <div class="container">
        <h1 style="font-size: 48px; color: beige; margin-bottom: 20px; font-family: 'Playfair Display', serif;">
            <?php echo esc_html(get_theme_mod('history_archive_title', 'История нашего храма')); ?>
        </h1>
        <p style="opacity: 0.9; max-width: 700px; margin: 0 auto; font-size: 18px; line-height: 1.7;">
            <?php echo esc_html(get_theme_mod('history_archive_description', 'Летопись прихода, важные события и воспоминания')); ?>
        </p>
        <div class="ornament-divider" style="margin-top: 30px; border-color: rgba(201,169,97,0.3);"></div>
    </div>
</section>

<section class="history-archive-section" style="padding: 80px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 1200px;">
        
        <!-- Фильтр по категориям (опционально) -->
        <?php
        $categories = get_terms(array(
            'taxonomy'   => 'history_category',
            'hide_empty' => true,
        ));
        if ($categories && !is_wp_error($categories)):
        ?>
        <div class="history-filters scroll-reveal" style="text-align: center; margin-bottom: 50px;">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="history-filter-btn active" data-category="">
                Все статьи
            </a>
            <?php foreach($categories as $cat): ?>
                <a href="?history-category=<?php echo esc_attr($cat->slug); ?>" 
                   class="history-filter-btn <?php echo isset($_GET['history-category']) && $_GET['history-category'] === $cat->slug ? 'active' : ''; ?>" 
                   data-category="<?php echo esc_attr($cat->slug); ?>">
                    <?php echo esc_html($cat->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Сетка архива -->
        <div class="history-content scroll-reveal delay-1">
            <?php 
            // Проверяем, есть ли фильтр по категории
            $category_filter = isset($_GET['history-category']) ? sanitize_text_field($_GET['history-category']) : '';
            $cat_shortcode = $category_filter ? ' category="' . esc_attr($category_filter) . '"' : '';
            echo do_shortcode('[church_history_archive count="12" order="DESC"' . $cat_shortcode . ']'); 
            ?>
        </div>
        
        <!-- Кнопка "Загрузить ещё" (если нужно) -->
        <div class="text-center" style="margin-top: 50px;">
            <a href="<?php echo esc_url(get_post_type_archive_link('church_history')); ?>" class="hero-button">
                Весь архив →
            </a>
        </div>
        
    </div>
</section>

<!-- ===== ХРОНОЛОГИЯ (ОПЦИОНАЛЬНО) ===== -->
<section class="history-timeline-section" style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 900px;">
        <div class="section-title scroll-reveal" style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-family: 'Playfair Display', serif;">Ключевые даты</h2>
            <div class="ornament-divider"></div>
        </div>
        
        <div class="history-timeline">
            <!-- Пример хронологии -->
            <div class="timeline-item scroll-reveal" style="display: grid; grid-template-columns: 150px 1fr; gap: 30px; margin-bottom: 40px; position: relative;">
                <div class="timeline-year" style="text-align: right; font-size: 24px; font-weight: 700; color: var(--color-accent); font-family: 'Playfair Display', serif;">
                    1893
                </div>
                <div class="timeline-content" style="padding: 25px; background: var(--color-bg); border-radius: 12px; border-left: 3px solid var(--color-accent);">
                    <h4 style="margin-bottom: 10px;">Закладка первого камня</h4>
                    <p style="color: var(--color-text-light); line-height: 1.7;">Начало строительства собора по проекту архитектора...</p>
                </div>
            </div>
            
            <div class="timeline-item scroll-reveal delay-1" style="display: grid; grid-template-columns: 150px 1fr; gap: 30px; margin-bottom: 40px; position: relative;">
                <div class="timeline-year" style="text-align: right; font-size: 24px; font-weight: 700; color: var(--color-accent); font-family: 'Playfair Display', serif;">
                    1903
                </div>
                <div class="timeline-content" style="padding: 25px; background: var(--color-bg); border-radius: 12px; border-left: 3px solid var(--color-accent);">
                    <h4 style="margin-bottom: 10px;">Освящение собора</h4>
                    <p style="color: var(--color-text-light); line-height: 1.7;">Торжественное освящение храма в присутствии...</p>
                </div>
            </div>
            
            <div class="timeline-item scroll-reveal delay-2" style="display: grid; grid-template-columns: 150px 1fr; gap: 30px; position: relative;">
                <div class="timeline-year" style="text-align: right; font-size: 24px; font-weight: 700; color: var(--color-accent); font-family: 'Playfair Display', serif;">
                    2024
                </div>
                <div class="timeline-content" style="padding: 25px; background: var(--color-bg); border-radius: 12px; border-left: 3px solid var(--color-accent);">
                    <h4 style="margin-bottom: 10px;">Реставрация икон</h4>
                    <p style="color: var(--color-text-light); line-height: 1.7;">Завершение реставрации старинных икон...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>