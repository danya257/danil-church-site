<?php
/**
 * Template Name: Новости
 */
get_header(); 
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: beige; margin-bottom: 15px; font-family: 'Playfair Display', serif;"><?php the_title(); ?></h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">События и объявления нашего прихода</p>
    </div>
</section>

<section class="news-full" style="padding: 80px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 1100px;">
        <div class="news-content scroll-reveal" style="background: white; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <?php 
            while (have_posts()): the_post();
                the_content();
            endwhile; 
            ?>
            
            <div style="margin-top: 50px;">
                <h3 style="text-align: center; margin-bottom: 30px; font-family: 'Playfair Display', serif;">Последние новости</h3>
                <?php echo do_shortcode('[church_news count="9"]'); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>