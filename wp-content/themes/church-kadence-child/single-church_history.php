<?php
/**
 * Template for single history article
 * Шаблон отдельной статьи архива
 */
get_header(); 
?>

<article class="history-article" style="background: var(--color-bg);">
    
    <!-- Заголовок с фото -->
    <header class="history-article-header" style="position: relative; height: 500px; overflow: hidden;">
        <?php if(has_post_thumbnail()): ?>
            <div style="position: absolute; inset: 0; background-size: cover; background-position: center;" 
                 style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>');">
            </div>
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(26,26,46,0.9), rgba(26,26,46,0.3));"></div>
        <?php else: ?>
            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, var(--color-primary), #2d2d44);"></div>
        <?php endif; ?>
        
        <div class="container" style="position: relative; z-index: 2; height: 100%; display: flex; align-items: flex-end; padding-bottom: 60px;">
            <div class="history-article-meta" style="color: white; max-width: 800px;">
                <div style="margin-bottom: 15px;">
                    <?php 
                    $categories = get_the_terms(get_the_ID(), 'history_category');
                    if ($categories && !is_wp_error($categories)): 
                    ?>
                        <span style="background: var(--color-accent); color: var(--color-primary); padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                            <?php echo esc_html($categories[0]->name); ?>
                        </span>
                    <?php endif; ?>
                    <span style="margin-left: 15px; opacity: 0.9;">
                        📅 <?php echo get_the_date('d.m.Y'); ?>
                    </span>
                </div>
                
                <h1 style="font-size: 48px; color: beige; margin-bottom: 20px; font-family: 'Playfair Display', serif; line-height: 1.2;">
                    <?php the_title(); ?>
                </h1>
                
                <?php if(has_excerpt()): ?>
                    <p style="font-size: 18px; opacity: 0.95; line-height: 1.7; max-width: 700px;">
                        <?php echo esc_html(get_the_excerpt()); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Контент статьи -->
    <div class="history-article-content" style="padding: 80px 0;">
        <div class="container" style="max-width: 800px;">
            <article class="article-body scroll-reveal" style="background: white; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                <?php 
                while (have_posts()): the_post();
                    the_content();
                endwhile; 
                ?>
            </article>
            
            <!-- Навигация -->
            <nav class="history-article-nav" style="margin-top: 50px; display: flex; justify-content: space-between; gap: 20px;">
                <?php 
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <?php if($prev_post): ?>
                    <a href="<?php echo get_permalink($prev_post->ID); ?>" class="history-nav-link" style="flex: 1; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-decoration: none; color: var(--color-text);">
                        <span style="font-size: 14px; color: var(--color-text-light);">← Предыдущая</span>
                        <h4 style="margin-top: 8px; font-size: 16px;"><?php echo esc_html(get_the_title($prev_post->ID)); ?></h4>
                    </a>
                <?php endif; ?>
                
                <?php if($next_post): ?>
                    <a href="<?php echo get_permalink($next_post->ID); ?>" class="history-nav-link" style="flex: 1; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-decoration: none; color: var(--color-text); text-align: right;">
                        <span style="font-size: 14px; color: var(--color-text-light);">Следующая →</span>
                        <h4 style="margin-top: 8px; font-size: 16px;"><?php echo esc_html(get_the_title($next_post->ID)); ?></h4>
                    </a>
                <?php endif; ?>
            </nav>
            
            <!-- Кнопка назад к архиву -->
            <div class="text-center" style="margin-top: 40px;">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('history'))); ?>" class="hero-button-outline" style="padding: 14px 35px; border-radius: 50px; border: 2px solid var(--color-primary); color: var(--color-primary); display: inline-block;">
                    ← Вернуться к архиву
                </a>
            </div>
        </div>
    </div>
    
</article>

<?php get_footer(); ?>