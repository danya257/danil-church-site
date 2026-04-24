<?php
/**
 * Template Name: Заказ треб
 * Страница для заказа и оплаты услуг
 */
get_header(); 
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: var(--color-gold-light); margin-bottom: 15px; font-family: 'Playfair Display', serif;"><?php the_title(); ?></h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Выберите требу для заказа и онлайн-оплаты</p>
    </div>
</section>

<section class="services-order-section" style="padding: 80px 0; background: var(--color-bg);">
    <div class="container" style="max-width: 1100px;">
        
        <div class="services-order-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php
            $services = new WP_Query(array(
                'post_type'      => 'church_service',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC'
            ));
            
            if ($services->have_posts()):
                while($services->have_posts()): $services->the_post();
                    $price = get_post_meta(get_the_ID(), '_service_price', true);
                    $price_type = get_post_meta(get_the_ID(), '_service_price_type', true);
                    $payment_enabled = get_post_meta(get_the_ID(), '_service_payment_enabled', true);
                    
                    // Формируем текст цены
                    $price_text = '';
                    if ($price_type === 'per_name') {
                        $price_text = $price . ' руб./имя';
                    } elseif ($price_type === 'per_10_names') {
                        $price_text = $price . ' руб./10 имён';
                    } else {
                        $price_text = $price . ' руб.';
                    }
                ?>
                <div class="service-order-card scroll-reveal" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                    <div style="margin-bottom: 15px;">
                        <?php if(has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 180px; object-fit: cover; border-radius: 8px;')); ?>
                        <?php else: ?>
                            <div style="width: 100%; height: 180px; background: linear-gradient(135deg, var(--color-primary), #2d2d44); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                ✝️
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h3 style="margin-bottom: 10px; font-family: 'Playfair Display', serif; font-size: 20px;"><?php the_title(); ?></h3>
                    
                    <?php if(has_excerpt()): ?>
                        <p style="color: var(--color-text-light); font-size: 14px; margin-bottom: 15px; line-height: 1.6;"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php endif; ?>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: var(--color-bg); border-radius: 8px;">
                        <span style="font-size: 14px; color: var(--color-text-light);">Стоимость:</span>
                        <span style="font-size: 18px; font-weight: 700; color: var(--color-accent);"><?php echo esc_html($price_text); ?></span>
                    </div>
                    
                    <button onclick="document.getElementById('service-modal-<?php echo get_the_ID(); ?>').style.display='flex'" class="hero-button" style="width: 100%; padding: 14px; cursor: pointer;">
                        💳 Заказать и оплатить
                    </button>
                    
                    <!-- Модальное окно с формой -->
                    <div id="service-modal-<?php echo get_the_ID(); ?>" class="service-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
                        <div style="background: white; border-radius: 12px; padding: 40px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
                            <button onclick="document.getElementById('service-modal-<?php echo get_the_ID(); ?>').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--color-text);">&times;</button>
                            
                            <?php echo do_shortcode('[church_service_order service_id="' . get_the_ID() . '"]'); ?>
                        </div>
                    </div>
                </div>
                <?php endwhile;
                wp_reset_postdata();
            else:
            ?>
            <p style="text-align: center; color: var(--color-text-light); grid-column: 1/-1;">Услуги пока не добавлены</p>
            <?php endif; ?>
        </div>
        
    </div>
</section>

<style>
.service-modal {
    animation: modalFadeIn 0.3s ease;
}
@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<?php get_footer(); ?>