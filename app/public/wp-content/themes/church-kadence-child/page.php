<?php
/**
 * Template for standard pages
 * Шаблон для обычных страниц (О храме, Контакты, etc.)
 */
get_header(); 
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 60px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: beige; margin-bottom: 10px; font-family: 'Playfair Display', serif;">
            <?php the_title(); ?>
        </h1>
        <?php if (has_excerpt()): ?>
            <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;"><?php the_excerpt(); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-content" style="padding: 60px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 800px;">
        <article class="page-article" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
            <?php 
            while (have_posts()): the_post();
                the_content();
            endwhile; 
            ?>
        </article>
    </div>
</section>

<?php get_footer(); ?>