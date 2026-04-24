<?php
/**
* Шаблон для одиночной новости
*/
get_header(); ?>

<main class="site-main" style="padding: 60px 0;">
    <article class="news-single" style="max-width:800px;margin:0 auto;background:#fff;padding:40px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <?php while(have_posts()): the_post(); ?>
        
        <div class="news-single-header" style="margin-bottom:30px;">
            <h1 class="news-single-title" style="font-size:32px;margin-bottom:15px;"><?php the_title(); ?></h1>
            <div class="news-single-meta" style="color:#666;font-size:14px;">
                <span class="news-date">📅 <?php echo get_the_date('d.m.Y'); ?></span>
                <?php if(has_term('', 'news_category')): ?>
                <span class="news-category" style="margin-left:15px;">📁 <?php echo get_the_term_list(get_the_ID(), 'news_category', '', ', '); ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(has_post_thumbnail()): ?>
        <div class="news-single-featured-image" style="margin-bottom:30px;border-radius:8px;overflow:hidden;">
            <?php the_post_thumbnail('large', array('loading' => 'lazy', 'style' => 'width:100%;height:auto;')); ?>
        </div>
        <?php endif; ?>
        
        <div class="news-single-content" style="line-height:1.8;color:#333;">
            <?php the_content(); ?>
        </div>
        
        <div class="news-single-footer" style="margin-top:40px;padding-top:20px;border-top:1px solid #eee;">
            <a href="<?php echo get_post_type_archive_link('church_news'); ?>" class="news-back-link" style="color:#c9a55c;text-decoration:none;">← Все новости</a>
        </div>
        
        <?php endwhile; ?>
    </article>
</main>

<?php get_footer(); ?>