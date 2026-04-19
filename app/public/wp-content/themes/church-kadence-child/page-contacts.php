<?php
/**
* Template Name: Страница контактов
*/
get_header(); ?>

<main class="site-main" style="padding:60px 0;">
    <div class="contacts-page" style="max-width:1200px;margin:0 auto;">
        
        <?php while(have_posts()): the_post(); ?>
        <h1 class="page-title" style="text-align:center;margin-bottom:40px;"><?php the_title(); ?></h1>
        
        <div class="contacts-content" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:40px;align-items:start;">
            
            <!-- Информация о контактах -->
            <div class="contacts-info">
                <?php 
                $contacts = church_get_contacts_settings();
                if (!empty($contacts['text'])): ?>
                    <div style="margin-bottom:30px;"><?php echo wp_kses_post($contacts['text']); ?></div>
                <?php endif; ?>
                
                <div class="contacts-details" style="background:#f8f9fa;padding:25px;border-radius:12px;">
                    <?php if (!empty($contacts['address'])): ?>
                    <p style="margin-bottom:15px;display:flex;align-items:start;gap:10px;">
                        <span>📍</span>
                        <span><?php echo esc_html($contacts['address']); ?></span>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($contacts['phone'])): ?>
                    <p style="margin-bottom:15px;display:flex;align-items:center;gap:10px;">
                        <span>📞</span>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contacts['phone'])); ?>" style="color:inherit;text-decoration:none;">
                            <?php echo esc_html($contacts['phone']); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($contacts['email'])): ?>
                    <p style="margin-bottom:15px;display:flex;align-items:center;gap:10px;">
                        <span>✉️</span>
                        <a href="mailto:<?php echo esc_attr($contacts['email']); ?>" style="color:inherit;text-decoration:none;">
                            <?php echo esc_html($contacts['email']); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($contacts['schedule'])): ?>
                    <p style="margin-bottom:0;display:flex;align-items:start;gap:10px;">
                        <span>🕐</span>
                        <span><?php echo esc_html($contacts['schedule']); ?></span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Карта -->
            <div class="contacts-map-wrapper">
                <?php echo church_display_contacts_map(); ?>
            </div>
            
        </div>
        
        <?php endwhile; ?>
        
    </div>
</main>

<?php get_footer(); ?>