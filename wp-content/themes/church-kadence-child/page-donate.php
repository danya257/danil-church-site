<?php
/**
 * Template Name: Пожертвования
 */
get_header(); 
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-secondary), #a63042); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: beige; margin-bottom: 15px; font-family: 'Playfair Display', serif;"><?php the_title(); ?></h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Ваша поддержка важна для нашего храма</p>
    </div>
</section>

<section class="donate-full" style="padding: 80px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 900px;">
        <div class="donate-content scroll-reveal" style="background: white; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <?php 
            while (have_posts()): the_post();
                the_content();
            endwhile; 
            ?>
            
            <div style="margin-top: 40px; padding: 30px; background: var(--color-bg); border-radius: 12px; border-left: 4px solid var(--color-accent);">
                <h3 style="margin-bottom: 20px; font-family: 'Playfair Display', serif;">Реквизиты для пожертвований</h3>
                <p><strong>Название:</strong> Местная религиозная организация...</p>
                <p><strong>ИНН:</strong> XX XXXXXXXXXX</p>
                <p><strong>Расчётный счёт:</strong> XXXXXXXXXXXXXXXXXXXXX</p>
                <p><strong>Банк:</strong> XXXXXXXXXXXXXXX</p>
            </div>
            
            <div style="margin-top: 40px; text-align: center;">
                <a href="#" class="donate-button" style="padding: 18px 50px; font-size: 18px;">
                    Пожертвовать онлайн
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>