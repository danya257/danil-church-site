<?php
/**
* Template Name:Архив новостей
*/
get_header(); ?>

<main class="site-main" style="padding: 60px 0;">
    <div class="news-archive-header" style="text-align:center;margin-bottom:40px;">
        <h1 class="news-archive-title">📰 Новости прихода</h1>
    </div>
    
    <div class="news-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:30px;max-width:1200px;margin:0 auto;">
        <?php if(have_posts()): $i = 0; while(have_posts()): the_post(); $i++; ?>
        <article class="news-card" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
            <?php if(has_post_thumbnail()): ?>
            <a href="<?php the_permalink(); ?>" class="news-image" style="display:block;height:200px;background-size:cover;background-position:center;background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>')"></a>
            <?php endif; ?>
            <div class="news-content" style="padding:20px;">
                <span class="news-date" style="color:#666;font-size:14px;"><?php echo get_the_date('d.m.Y'); ?></span>
                <h3 style="margin:10px 0;"><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:#333;"><?php the_title(); ?></a></h3>
                <?php if(has_excerpt()): ?>
                <p class="news-excerpt" style="color:#555;line-height:1.6;"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="news-read-more" style="color:#c9a55c;text-decoration:none;font-weight:600;">Читать далее <span>→</span></a>
            </div>
        </article>
        <?php endwhile; else: ?>
        <p style="text-align:center;grid-column:1/-1;">Новостей пока нет.</p>
        <?php endif; ?>
    </div>
    
    <div class="news-pagination" style="text-align:center;margin-top:40px;">
        <?php 
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '← Назад',
            'next_text' => 'Вперёд →',
        )); 
        ?>
    </div>
</main>

<?php get_footer(); ?>