<?php
/**
 * Church Kadence Child - Functions
 * ИТОГОВАЯ ВЕРСИЯ (Слайдер + Контакты + Новости + Заказы)
 * Без дубликатов и конфликтов
 */

// ===== 1. ПОДКЛЮЧЕНИЕ WALKER ДЛЯ МЕНЮ =====
if (file_exists(get_stylesheet_directory() . '/inc/class-walker-nav-menu.php')) {
    require_once get_stylesheet_directory() . '/inc/class-walker-nav-menu.php';
}

// ===== 2. ПОДДЕРЖКА МЕТА-ПОЛЕЙ В REST API =====
function church_service_register_meta() {
    $meta_fields = array(
        '_service_price'              => 'number',
        '_service_price_type'         => 'string',
        '_service_min_names'          => 'number',
        '_service_max_names'          => 'number',
        '_service_payment_enabled'    => 'boolean',
        '_service_payment_description'=> 'string',
    );
    foreach ($meta_fields as $key => $type) {
        register_post_meta('church_service', $key, array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => $type,
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));
    }
}
add_action('init', 'church_service_register_meta');

// ===== 3. ПОДКЛЮЧЕНИЕ СТИЛЕЙ И СКРИПТОВ =====
function church_child_enqueue_assets() {
    wp_enqueue_style(
        'kadence-parent',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme(get_template())->get('Version')
    );
    
    wp_enqueue_style(
        'church-child',
        get_stylesheet_uri(),
        array('kadence-parent'),
        wp_get_theme()->get('Version')
    );
    
    wp_enqueue_style(
        'church-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap',
        array(),
        null
    );
    
    $js_file = get_stylesheet_directory() . '/assets/js/main.js';
    if (file_exists($js_file)) {
        wp_enqueue_script(
            'church-main',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            array('jquery'),
            '1.0',
            true
        );
        wp_localize_script('church-main', 'churchData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'homeUrl' => home_url('/'),
            'nonce'   => wp_create_nonce('church_order_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'church_child_enqueue_assets', 20);

// ===== 4. РЕГИСТРАЦИЯ МЕНЮ =====
function church_child_register_menus() {
    register_nav_menus(array(
        'primary' => __('Главное меню', 'church-kadence-child'),
        'footer'  => __('Меню в подвале', 'church-kadence-child')
    ));
}
add_action('after_setup_theme', 'church_child_register_menus');

// ===== 5. ПОДДЕРЖКА ТЕМЫ =====
function church_child_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ));
}
add_action('after_setup_theme', 'church_child_setup');

// ===== 6. CPT: УСЛУГИ/ТРЕБЫ =====
function church_register_services_cpt() {
    $labels = array(
        'name'               => 'Требы и услуги',
        'singular_name'      => 'Услуга',
        'menu_name'          => 'Требы и услуги',
        'add_new'            => 'Добавить требу',
        'add_new_item'       => 'Добавить новую требу',
        'edit_item'          => 'Редактировать требу',
        'view_item'          => 'Просмотр требу',
        'search_items'       => 'Поиск треб',
        'not_found'          => 'Треб не найдено',
        'not_found_in_trash' => 'В корзине нет треб'
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-heart',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'            => array('slug' => 'services', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 5,
        'publicly_queryable' => true,
    );
    
    register_post_type('church_service', $args);
    
    register_taxonomy('service_category', 'church_service', array(
        'labels'       => array('name' => 'Категории треб', 'singular_name' => 'Категория'),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'service-category')
    ));
}
add_action('init', 'church_register_services_cpt', 1);

// ===== 7. CPT: НОВОСТИ =====
function church_register_news_cpt() {
    $labels = array(
        'name'               => 'Новости прихода',
        'singular_name'      => 'Новость',
        'menu_name'          => 'Новости',
        'add_new'            => 'Добавить новость',
        'add_new_item'       => 'Добавить новую новость',
        'edit_item'          => 'Редактировать новость',
        'view_item'          => 'Просмотр новости',
        'search_items'       => 'Поиск новостей',
        'not_found'          => 'Новостей не найдено',
        'not_found_in_trash' => 'В корзине нет новостей',
        'all_items'          => 'Все новости',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-megaphone',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'),
        'rewrite'            => array('slug' => 'news', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 4,
        'publicly_queryable' => true,
    );
    
    register_post_type('church_news', $args);
    
    register_taxonomy('news_category', 'church_news', array(
        'labels'       => array('name' => 'Категории новостей', 'singular_name' => 'Категория'),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'news-category')
    ));
}
add_action('init', 'church_register_news_cpt', 1);

// ===== 8. CPT: ИСТОРИЯ =====
function church_register_history_cpt() {
    $labels = array(
        'name'               => 'История храма',
        'singular_name'      => 'Статья архива',
        'menu_name'          => 'История храма',
        'add_new'            => 'Добавить статью',
        'add_new_item'       => 'Добавить новую статью',
        'edit_item'          => 'Редактировать статью',
        'view_item'          => 'Просмотр статьи',
        'search_items'       => 'Поиск в архиве',
        'not_found'          => 'Статей не найдено',
        'not_found_in_trash' => 'В корзине нет статей',
        'all_items'          => 'Все статьи архива'
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-archive',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'),
        'rewrite'            => array('slug' => 'history-archive', 'with_front' => false),
        'show_in_rest'       => true,
        'menu_position'      => 6,
        'publicly_queryable' => true,
    );
    
    register_post_type('church_history', $args);
    
    register_taxonomy('history_category', 'church_history', array(
        'labels'       => array('name' => 'Категории истории', 'singular_name' => 'Категория'),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'history-category')
    ));
}
add_action('init', 'church_register_history_cpt', 1);

// ===== 9. CPT: ЗАКАЗЫ =====
function church_register_orders_cpt() {
    $labels = array(
        'name'               => 'Заказы треб',
        'singular_name'      => 'Заказ',
        'menu_name'          => 'Заказы',
        'add_new'            => 'Добавить заказ',
        'add_new_item'       => 'Добавить новый заказ',
        'edit_item'          => 'Редактировать заказ',
        'view_item'          => 'Просмотр заказа',
        'search_items'       => 'Поиск заказов',
        'not_found'          => 'Заказов не найдено',
        'not_found_in_trash' => 'В корзине нет заказов',
        'all_items'          => 'Все заказы'
    );
    
    $args = array(
        'labels'           => $labels,
        'public'           => false,
        'show_ui'          => true,
        'menu_icon'        => 'dashicons-clipboard',
        'supports'         => array('title'),
        'menu_position'    => 7,
        'capability_type'  => 'post'
    );
    
    register_post_type('church_order', $args);
}
add_action('init', 'church_register_orders_cpt', 1);

// ===== 10. МЕТА-БОКСЫ ДЛЯ УСЛУГ =====
function church_service_meta_boxes() {
    add_meta_box(
        'church_service_price_box',
        '💰 Стоимость услуги',
        'church_service_price_field',
        'church_service',
        'side',
        'high'
    );
    add_meta_box(
        'church_service_payment_box',
        '⚙️ Настройки оплаты',
        'church_service_payment_field',
        'church_service',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'church_service_meta_boxes');

function church_service_price_field($post) {
    wp_nonce_field('church_service_save_data', 'church_service_nonce');
    $price         = get_post_meta($post->ID, '_service_price', true);
    $price_type    = get_post_meta($post->ID, '_service_price_type', true);
    $min_names     = get_post_meta($post->ID, '_service_min_names', true);
    $max_names     = get_post_meta($post->ID, '_service_max_names', true);
    
    $price         = $price !== '' ? $price : '0';
    $price_type    = $price_type ? $price_type : 'fixed';
    $min_names     = $min_names ? $min_names : '1';
    $max_names     = $max_names ? $max_names : '10';
    ?>
    <p>
        <label for="service_price">💰 Цена (рублей):</label>
        <input type="number" id="service_price" name="service_price" 
               value="<?php echo esc_attr($price); ?>" min="0" step="1" 
               style="width: 100%; padding: 8px; margin-top: 5px;">
    </p>
    <p>
        <label for="service_price_type">📊 Тип цены:</label>
        <select id="service_price_type" name="service_price_type" 
                style="width: 100%; padding: 8px; margin-top: 5px;">
            <option value="fixed" <?php selected($price_type, 'fixed'); ?>>🔹 Фиксированная</option>
            <option value="per_name" <?php selected($price_type, 'per_name'); ?>>👤 За 1 имя</option>
            <option value="per_10_names" <?php selected($price_type, 'per_10_names'); ?>>🔟 За 10 имён</option>
        </select>
    </p>
    <p>
        <label for="service_min_names">📉 Мин. имён:</label>
        <input type="number" id="service_min_names" name="service_min_names" 
               value="<?php echo esc_attr($min_names); ?>" min="1" 
               style="width: 100%; padding: 8px; margin-top: 5px;">
    </p>
    <p>
        <label for="service_max_names">📈 Макс. имён:</label>
        <input type="number" id="service_max_names" name="service_max_names" 
               value="<?php echo esc_attr($max_names); ?>" min="1" 
               style="width: 100%; padding: 8px; margin-top: 5px;">
    </p>
    <?php
}

function church_service_payment_field($post) {
    $payment_enabled     = get_post_meta($post->ID, '_service_payment_enabled', true);
    $payment_description = get_post_meta($post->ID, '_service_payment_description', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="service_payment_enabled" value="1" 
                   <?php checked($payment_enabled, '1'); ?>> 
            ✅ Включить онлайн-оплату
        </label>
    </p>
    <p>
        <label for="service_payment_description">📝 Описание:</label>
        <textarea id="service_payment_description" name="service_payment_description" 
                  rows="3" style="width: 100%; margin-top: 5px;"><?php 
            echo esc_textarea($payment_description); 
        ?></textarea>
    </p>
    <?php
}

function church_service_save_fields($post_id) {
    if (!isset($_POST['church_service_nonce']) || 
        !wp_verify_nonce($_POST['church_service_nonce'], 'church_service_save_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'church_service') return;
    
    if (isset($_POST['service_price'])) {
        update_post_meta($post_id, '_service_price', floatval($_POST['service_price']));
    }
    if (isset($_POST['service_price_type'])) {
        update_post_meta($post_id, '_service_price_type', sanitize_text_field($_POST['service_price_type']));
    }
    if (isset($_POST['service_min_names'])) {
        update_post_meta($post_id, '_service_min_names', sanitize_text_field($_POST['service_min_names']));
    }
    if (isset($_POST['service_max_names'])) {
        update_post_meta($post_id, '_service_max_names', sanitize_text_field($_POST['service_max_names']));
    }
    update_post_meta($post_id, '_service_payment_enabled', 
                     isset($_POST['service_payment_enabled']) ? '1' : '0');
    if (isset($_POST['service_payment_description'])) {
        update_post_meta($post_id, '_service_payment_description', 
                        sanitize_textarea_field($_POST['service_payment_description']));
    }
}
add_action('save_post_church_service', 'church_service_save_fields');

// ===== 11. НАСТРОЙКИ ПЛАТЕЖЕЙ (АДМИНКА) =====
function church_payment_settings_page() {
    add_menu_page(
        'Настройки платежей',
        '💳 Платежи',
        'manage_options',
        'church-payments',
        'church_payment_settings_html',
        'dashicons-credit-card',
        6
    );
}
add_action('admin_menu', 'church_payment_settings_page');

function church_payment_settings_html() {
    if (isset($_POST['church_payment_save'])) {
        check_admin_referer('church_payment_settings', 'church_payment_nonce');
        update_option('church_payment_enabled', isset($_POST['payment_enabled']) ? '1' : '0');
        update_option('church_payment_system', sanitize_text_field($_POST['payment_system']));
        update_option('church_payment_shop_id', sanitize_text_field($_POST['shop_id']));
        update_option('church_payment_secret_key', sanitize_text_field($_POST['secret_key']));
        update_option('church_payment_email', sanitize_email($_POST['payment_email']));
        echo '<div class="notice notice-success"><p>Настройки сохранены!</p></div>';
    }
    
    $enabled     = get_option('church_payment_enabled', '0');
    $system      = get_option('church_payment_system', 'yookassa');
    $shop_id     = get_option('church_payment_shop_id', '');
    $secret_key  = get_option('church_payment_secret_key', '');
    $email       = get_option('church_payment_email', get_option('admin_email'));
    ?>
    <div class="wrap">
        <h1>💳 Настройки платёжной системы</h1>
        <form method="post">
            <?php wp_nonce_field('church_payment_settings', 'church_payment_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>Включить оплату:</th>
                    <td><input type="checkbox" name="payment_enabled" value="1" 
                        <?php checked($enabled, '1'); ?>></td>
                </tr>
                <tr>
                    <th>Платёжная система:</th>
                    <td>
                        <select name="payment_system">
                            <option value="yookassa" <?php selected($system, 'yookassa'); ?>>ЮKassa</option>
                            <option value="robokassa" <?php selected($system, 'robokassa'); ?>>Robokassa</option>
                            <option value="manual" <?php selected($system, 'manual'); ?>>Ручной перевод</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Shop ID:</th>
                    <td><input type="text" name="shop_id" value="<?php echo esc_attr($shop_id); ?>" 
                        class="regular-text"></td>
                </tr>
                <tr>
                    <th>Секретный ключ:</th>
                    <td><input type="password" name="secret_key" value="<?php echo esc_attr($secret_key); ?>" 
                        class="regular-text"></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><input type="email" name="payment_email" value="<?php echo esc_attr($email); ?>" 
                        class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button('Сохранить', 'primary', 'church_payment_save'); ?>
        </form>
    </div>
    <?php
}

// ===== 12. НАСТРОЙКИ TELEGRAM =====
function church_telegram_settings_page() {
    add_submenu_page(
        'church-payments',
        'Настройки Telegram',
        '📬 Telegram',
        'manage_options',
        'church-telegram',
        'church_telegram_settings_html'
    );
}
add_action('admin_menu', 'church_telegram_settings_page');

function church_telegram_settings_html() {
    if (isset($_POST['church_telegram_save'])) {
        check_admin_referer('church_telegram_settings', 'church_telegram_nonce');
        update_option('church_telegram_enabled', isset($_POST['telegram_enabled']) ? '1' : '0');
        update_option('church_telegram_bot_token', sanitize_text_field($_POST['bot_token']));
        update_option('church_telegram_chat_id', sanitize_text_field($_POST['chat_id']));
        echo '<div class="notice notice-success"><p>Настройки Telegram сохранены!</p></div>';
    }
    
    $enabled   = get_option('church_telegram_enabled', '0');
    $bot_token = get_option('church_telegram_bot_token', '');
    $chat_id   = get_option('church_telegram_chat_id', '');
    ?>
    <div class="wrap">
        <h1>📬 Настройки Telegram бота</h1>
        <form method="post">
            <?php wp_nonce_field('church_telegram_settings', 'church_telegram_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>Включить уведомления:</th>
                    <td><input type="checkbox" name="telegram_enabled" value="1" 
                        <?php checked($enabled, '1'); ?>></td>
                </tr>
                <tr>
                    <th>Token бота:</th>
                    <td><input type="text" name="bot_token" value="<?php echo esc_attr($bot_token); ?>" 
                        class="regular-text"></td>
                </tr>
                <tr>
                    <th>Chat ID:</th>
                    <td><input type="text" name="chat_id" value="<?php echo esc_attr($chat_id); ?>" 
                        class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button('Сохранить', 'primary', 'church_telegram_save'); ?>
        </form>
        <hr>
        <h3>🧪 Тест:</h3>
        <form method="post">
            <?php wp_nonce_field('church_telegram_test', 'church_telegram_test_nonce'); ?>
            <button type="submit" name="church_telegram_test" class="button">📤 Тест</button>
        </form>
        <?php if (isset($_POST['church_telegram_test'])) {
            check_admin_referer('church_telegram_test', 'church_telegram_test_nonce');
            $result = church_send_telegram_message("🔔 Тест: " . current_time('d.m.Y H:i'));
            echo $result 
                ? '<div class="notice notice-success"><p>✅ Отправлено!</p></div>' 
                : '<div class="notice notice-error"><p>❌ Ошибка</p></div>';
        } ?>
    </div>
    <?php
}

// ===== 13. ОТПРАВКА TELEGRAM =====
function church_send_telegram_message($message) {
    $enabled   = get_option('church_telegram_enabled', '0');
    $bot_token = trim(get_option('church_telegram_bot_token', ''));
    $bot_token = preg_replace('/\s+/', '', $bot_token);
    $chat_id   = trim(get_option('church_telegram_chat_id', ''));
    
    if ($enabled !== '1' || empty($bot_token) || empty($chat_id)) {
        return false;
    }
    
    $url  = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $data = array('chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML');
    $args = array(
        'body'        => json_encode($data),
        'timeout'     => 15,
        'sslverify'   => true,
        'headers'     => array('Content-Type' => 'application/json')
    );
    
    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) return false;
    
    $body   = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    
    return isset($result['ok']) && $result['ok'] === true;
}

// ===== 14. ОТПРАВКА EMAIL =====
function church_send_order_email($order_data) {
    $admin_email = get_option('church_payment_email', get_option('admin_email'));
    $subject     = '🕊️ Новый заказ #' . $order_data['order_id'];
    $message     = "<html><body>
        <h1>Заказ #{$order_data['order_id']}</h1>
        <p>Услуга: {$order_data['service_name']}</p>
        <p>Клиент: {$order_data['customer_name']}</p>
        <p>Телефон: {$order_data['customer_phone']}</p>
        <p>Сумма: {$order_data['total_amount']} руб.</p>
    </body></html>";
    $headers     = array('Content-Type: text/html; charset=UTF-8');
    
    wp_mail($admin_email, $subject, $message, $headers);
}

// ===== 15. ЗАГЛУШКА YOOKASSA =====
function church_create_yookassa_payment($order_id, $amount, $customer_email, $customer_name) {
    update_post_meta($order_id, '_payment_status', 'pending');
    return array(
        'success'     => true,
        'payment_url' => home_url('/payment-success?order=' . $order_id . '&test=1'),
        'status'      => 'pending'
    );
}

// ===== 16. ОБРАБОТКА ЗАКАЗА AJAX =====
function church_submit_service_order_ajax() {
    if (!isset($_POST['church_order_nonce']) || 
        !wp_verify_nonce($_POST['church_order_nonce'], 'church_order_nonce')) {
        wp_send_json_error(array('message' => 'Ошибка безопасности'));
    }
    
    $service_id     = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
    $customer_name  = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
    $customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
    $customer_phone = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
    $names_count    = isset($_POST['names_count']) ? intval($_POST['names_count']) : 1;
    $names_list     = isset($_POST['names_list']) ? sanitize_textarea_field($_POST['names_list']) : '';
    $order_comment  = isset($_POST['order_comment']) ? sanitize_textarea_field($_POST['order_comment']) : '';
    
    if (empty($customer_name) || empty($customer_email) || empty($customer_phone)) {
        wp_send_json_error(array('message' => 'Заполните все поля'));
    }
    if ($service_id <= 0) {
        wp_send_json_error(array('message' => 'Неверный ID услуги'));
    }
    
    $service      = get_post($service_id);
    $service_name = $service ? $service->post_title : 'Неизвестная услуга';
    $price        = floatval(get_post_meta($service_id, '_service_price', true));
    $price_type   = get_post_meta($service_id, '_service_price_type', true);
    
    if ($price_type === 'per_name') {
        $total = $price * $names_count;
    } elseif ($price_type === 'per_10_names') {
        $total = ceil($names_count / 10) * $price;
    } else {
        $total = $price;
    }
    
    $order_id = wp_insert_post(array(
        'post_title'  => 'Заказ #' . time(),
        'post_type'   => 'church_order',
        'post_status' => 'publish'
    ));
    
    if (is_wp_error($order_id)) {
        wp_send_json_error(array('message' => 'Не удалось создать заказ'));
    }
    
    update_post_meta($order_id, '_service_id', $service_id);
    update_post_meta($order_id, '_service_name', $service_name);
    update_post_meta($order_id, '_customer_name', $customer_name);
    update_post_meta($order_id, '_customer_email', $customer_email);
    update_post_meta($order_id, '_customer_phone', $customer_phone);
    update_post_meta($order_id, '_names_count', $names_count);
    update_post_meta($order_id, '_names_list', $names_list);
    update_post_meta($order_id, '_order_comment', $order_comment);
    update_post_meta($order_id, '_total_amount', $total);
    update_post_meta($order_id, '_order_date', current_time('mysql'));
    
    $order_data = array(
        'order_id'       => $order_id,
        'service_name'   => $service_name,
        'customer_name'  => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'names_count'    => $names_count,
        'names_list'     => $names_list,
        'total_amount'   => $total
    );
    
    church_send_order_email($order_data);
    
    $telegram_message = "🕊️ <b>Новый заказ</b>
#{$order_id}
📝 {$service_name}
👤 {$customer_name}
📞 {$customer_phone}
💰 {$total} руб.";
    church_send_telegram_message($telegram_message);
    
    $payment_enabled = get_option('church_payment_enabled', '0');
    $payment_url     = '';
    
    if ($payment_enabled === '1') {
        $payment_system = get_option('church_payment_system', 'yookassa');
        if ($payment_system === 'yookassa') {
            $payment_result = church_create_yookassa_payment($order_id, $total, $customer_email, $customer_name);
            $payment_url    = $payment_result['payment_url'];
        }
    }
    
    wp_send_json_success(array(
        'order_id'    => $order_id,
        'payment_url' => $payment_url,
        'total'       => $total,
        'message'     => 'Заказ оформлен!'
    ));
}
add_action('wp_ajax_church_submit_service_order', 'church_submit_service_order_ajax');
add_action('wp_ajax_nopriv_church_submit_service_order', 'church_submit_service_order_ajax');

// ===== 17. SHORTCODE: ФОРМА ЗАКАЗА =====
function church_service_order_form_shortcode($atts) {
    $atts = shortcode_atts(array('service_id' => 0), $atts);
    if (!$atts['service_id']) {
        return '<p style="color:red;">Ошибка: нет ID услуги</p>';
    }
    
    $service = get_post($atts['service_id']);
    if (!$service || $service->post_type !== 'church_service') {
        return '<p style="color:red;">Ошибка: услуга не найдена</p>';
    }
    
    $price         = floatval(get_post_meta($atts['service_id'], '_service_price', true));
    $price_type    = get_post_meta($atts['service_id'], '_service_price_type', true);
    $min_names     = get_post_meta($atts['service_id'], '_service_min_names', true) ?: 1;
    $max_names     = get_post_meta($atts['service_id'], '_service_max_names', true) ?: 10;
    $payment_enabled = get_post_meta($atts['service_id'], '_service_payment_enabled', true);
    $nonce         = wp_create_nonce('church_order_nonce');
    
    ob_start();
    ?>
    <div class="church-service-order-form" data-service-id="<?php echo esc_attr($atts['service_id']); ?>">
        <h3 style="margin-bottom:20px;"><?php echo esc_html($service->post_title); ?></h3>
        <form class="service-order-form">
            <input type="hidden" name="service_id" value="<?php echo esc_attr($atts['service_id']); ?>">
            <input type="hidden" name="service_price" value="<?php echo esc_attr($price); ?>">
            <input type="hidden" name="service_price_type" value="<?php echo esc_attr($price_type); ?>">
            <input type="hidden" name="church_order_nonce" value="<?php echo esc_attr($nonce); ?>">
            
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">Ваше имя:</label>
                <input type="text" name="customer_name" required 
                       style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">Email:</label>
                <input type="email" name="customer_email" required 
                       style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">Телефон:</label>
                <input type="tel" name="customer_phone" required 
                       style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            </div>
            
            <?php if ($price_type !== 'fixed'): ?>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">
                    Количество имён (<?php echo $min_names; ?>-<?php echo $max_names; ?>):
                </label>
                <input type="number" name="names_count" 
                       min="<?php echo esc_attr($min_names); ?>" 
                       max="<?php echo esc_attr($max_names); ?>" 
                       value="<?php echo esc_attr($min_names); ?>" 
                       style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <?php endif; ?>
            
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">Имена (через запятую):</label>
                <textarea name="names_list" rows="4" required 
                          style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;"></textarea>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;">Комментарий:</label>
                <textarea name="order_comment" rows="2" 
                          style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;"></textarea>
            </div>
            
            <div style="background:#f5f5f5;padding:20px;border-radius:12px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;">
                    <span>Итого:</span>
                    <span class="total-amount" style="font-size:24px;font-weight:700;">
                        <?php echo esc_html($price); ?> руб.
                    </span>
                </div>
            </div>
            
            <button type="submit" class="hero-button" style="width:100%;padding:18px;font-size:16px;">
                <?php echo $payment_enabled === '1' ? '💳 Оплатить' : '📝 Отправить'; ?>
            </button>
            <div class="form-message" style="display:none;margin-top:15px;padding:15px;border-radius:8px;text-align:center;"></div>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.service-order-form');
        forms.forEach(function(form) {
            const namesInput   = form.querySelector('input[name="names_count"]');
            const totalAmount  = form.querySelector('.total-amount');
            const basePrice    = <?php echo floatval($price); ?>;
            const priceType    = '<?php echo esc_js($price_type); ?>';
            
            if (namesInput) {
                namesInput.addEventListener('input', function() {
                    let count = parseInt(this.value) || 0;
                    let total = priceType === 'per_name' 
                        ? basePrice * count 
                        : (priceType === 'per_10_names' ? Math.ceil(count / 10) * basePrice : basePrice);
                    if (totalAmount) totalAmount.textContent = total + ' руб.';
                });
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData    = new FormData(form);
                formData.append('action', 'church_submit_service_order');
                const btn         = form.querySelector('button[type="submit"]');
                const messageDiv  = form.querySelector('.form-message');
                const originalText = btn.innerText;
                
                btn.disabled = true;
                btn.innerText = 'Отправка...';
                messageDiv.style.display = 'none';
                
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    messageDiv.style.display = 'block';
                    if (data.success) {
                        messageDiv.style.background = '#d4edda';
                        messageDiv.style.color = '#155724';
                        messageDiv.textContent = data.data.message;
                        if (data.data.payment_url) {
                            setTimeout(() => window.location.href = data.data.payment_url, 1000);
                            return;
                        }
                        form.reset();
                    } else {
                        messageDiv.style.background = '#f8d7da';
                        messageDiv.style.color = '#721c24';
                        messageDiv.textContent = 'Ошибка: ' + (data.data.message || 'Неизвестная ошибка');
                    }
                })
                .catch(error => {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.textContent = 'Ошибка соединения';
                    console.error('Form error:', error);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('church_service_order', 'church_service_order_form_shortcode');

// ===== 18. SHORTCODE: НОВОСТИ =====
function church_news_preview_shortcode($atts) {
    $atts = shortcode_atts(array('count' => 6, 'category' => ''), $atts);
    
    $query_args = array(
        'post_type'      => 'church_news',
        'posts_per_page' => intval($atts['count']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    
    if (!empty($atts['category'])) {
        $query_args['tax_query'] = array(array(
            'taxonomy' => 'news_category',
            'field'    => 'slug',
            'terms'    => $atts['category']
        ));
    }
    
    $query = new WP_Query($query_args);
    if (!$query->have_posts()) {
        return '<p style="text-align:center;">Новостей пока нет.</p>';
    }
    
    ob_start();
    ?>
    <div class="news-grid">
        <?php $i = 0; while($query->have_posts()): $query->the_post(); $i++; ?>
        <article class="news-card scroll-reveal delay-<?php echo ($i % 3 + 1); ?>">
            <?php if(has_post_thumbnail()): ?>
            <a href="<?php the_permalink(); ?>" class="news-image" 
               style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>')">
            </a>
            <?php endif; ?>
            <div class="news-content">
                <span class="news-date"><?php echo get_the_date('d.m.Y'); ?></span>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php if(has_excerpt()): ?>
                <p class="news-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="news-read-more">Читать далее <span>→</span></a>
            </div>
        </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('church_news', 'church_news_preview_shortcode');

// ===== 19. SHORTCODE: СЕТКА УСЛУГ =====
function church_services_grid_shortcode($atts) {
    $atts = shortcode_atts(array('count' => 6, 'order' => 'ASC', 'category' => ''), $atts);
    
    $query_args = array(
        'post_type'      => 'church_service',
        'posts_per_page' => intval($atts['count']),
        'orderby'        => 'menu_order',
        'order'          => $atts['order'],
        'post_status'    => 'publish'
    );
    
    if (!empty($atts['category'])) {
        $query_args['tax_query'] = array(array(
            'taxonomy' => 'service_category',
            'field'    => 'slug',
            'terms'    => $atts['category']
        ));
    }
    
    $query = new WP_Query($query_args);
    if (!$query->have_posts()) return '';
    
    ob_start();
    ?>
    <div class="services-grid">
        <?php $i = 0; while($query->have_posts()): $query->the_post(); $i++; ?>
        <article class="service-card scroll-reveal delay-<?php echo ($i % 4 + 1); ?>">
            <?php if(has_post_thumbnail()): ?>
            <div class="service-icon">
                <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
            </div>
            <?php else: ?>
            <div class="service-icon">✝️</div>
            <?php endif; ?>
            <h3><?php the_title(); ?></h3>
            <?php if(has_excerpt()): ?>
            <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
            <?php endif; ?>
            <a href="<?php the_permalink(); ?>" class="service-link">Подробнее <span>→</span></a>
        </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('church_services', 'church_services_grid_shortcode');

// ===== 20. SHORTCODE: РАСПИСАНИЕ =====
function church_schedule_shortcode($atts) {
    $schedule = array(
        array('day' => 'Суббота', 'service' => 'Всенощное бдение', 'time' => '17:00'),
        array('day' => 'Воскресенье', 'service' => 'Божественная литургия', 'time' => '09:00'),
        array('day' => 'Среда', 'service' => 'Акафист св. Александру Невскому', 'time' => '18:00'),
        array('day' => 'Пятница', 'service' => 'Литургия Преждеосвященных Даров', 'time' => '09:00')
    );
    
    ob_start();
    ?>
    <div class="schedule-table">
        <?php foreach($schedule as $item): ?>
        <div class="schedule-row scroll-reveal from-bottom">
            <span class="schedule-day"><?php echo esc_html($item['day']); ?></span>
            <span class="schedule-service"><?php echo esc_html($item['service']); ?></span>
            <span class="schedule-time"><?php echo esc_html($item['time']); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('church_schedule', 'church_schedule_shortcode');

// ===== 21. SHORTCODE: АРХИВ ИСТОРИИ =====
function church_history_archive_shortcode($atts) {
    $atts = shortcode_atts(array('count' => 12, 'order' => 'DESC', 'category' => ''), $atts);
    
    $query = new WP_Query(array(
        'post_type'      => 'church_history',
        'posts_per_page' => intval($atts['count']),
        'orderby'        => 'date',
        'order'          => $atts['order'],
        'post_status'    => 'publish'
    ));
    
    if (!$query->have_posts()) {
        return '<p style="text-align:center;">В архиве пока нет статей.</p>';
    }
    
    ob_start();
    ?>
    <div class="history-archive-grid">
        <?php $i = 0; while($query->have_posts()): $query->the_post(); $i++; ?>
        <article class="history-card scroll-reveal delay-<?php echo ($i % 3 + 1); ?>">
            <a href="<?php the_permalink(); ?>" class="history-card-link"></a>
            <?php if(has_post_thumbnail()): ?>
            <div class="history-card-image-wrapper">
                <div class="history-card-image">
                    <?php the_post_thumbnail('large', array('loading' => 'lazy')); ?>
                </div>
            </div>
            <?php else: ?>
            <div class="history-card-image-wrapper">
                <div class="history-card-image history-card-image-placeholder">
                    <span>📜</span>
                </div>
            </div>
            <?php endif; ?>
            <div class="history-card-content-wrapper">
                <div class="history-card-content">
                    <div class="history-card-header">
                        <h3 class="history-card-title"><?php the_title(); ?></h3>
                    </div>
                    <div class="history-card-meta">
                        <span class="history-card-date">
                            📅 <?php echo get_the_date('d.m.Y'); ?>
                        </span>
                    </div>
                    <div class="history-card-text">
                        <?php if(has_excerpt()): ?>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                        <?php else: ?>
                        <p><?php echo wp_trim_words(get_the_content(), 35); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="history-card-footer">
                        <span class="history-read-more">Читать статью <span>→</span></span>
                    </div>
                </div>
            </div>
        </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('church_history_archive', 'church_history_archive_shortcode');

// ===== 22. SHORTCODE: СЛАЙДЕР НА ГЛАВНУЮ =====
function church_home_slider_shortcode($atts) {
    $settings = church_get_slider_settings();
    if (empty($settings['slides'])) return '';
    
    $slider_id = 'church-home-slider-' . uniqid();
    
    ob_start();
    ?>
    <div class="church-home-slider" id="<?php echo esc_attr($slider_id); ?>" 
         data-auto="<?php echo $settings['auto'] ? '1' : '0'; ?>" 
         data-speed="<?php echo esc_attr($settings['speed']); ?>">
        <div class="slider-container">
            <?php foreach ($settings['slides'] as $index => $slide): ?>
            <div class="slider-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('<?php echo esc_url($slide['image']); ?>');">
                <div class="slider-overlay"></div>
                <div class="slider-content">
                    <?php if (!empty($slide['title'])): ?>
                    <h2 class="slider-title"><?php echo esc_html($slide['title']); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($slide['subtitle'])): ?>
                    <p class="slider-subtitle"><?php echo esc_html($slide['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($slide['link'])): ?>
                    <a href="<?php echo esc_url($slide['link']); ?>" class="slider-button">Подробнее</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="slider-nav slider-prev" aria-label="Предыдущий слайд">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button class="slider-nav slider-next" aria-label="Следующий слайд">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        <div class="slider-dots">
            <?php foreach ($settings['slides'] as $index => $slide): ?>
            <button class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                    data-index="<?php echo $index; ?>" 
                    aria-label="Слайд <?php echo $index + 1; ?>">
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
    .church-home-slider { position: relative; width: 100%; height: 100vh; max-height: 700px; min-height: 400px; overflow: hidden; background: #000; }
    .slider-container { position: relative; width: 100%; height: 100%; }
    .slider-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s ease-in-out; display: flex; align-items: center; justify-content: center; }
    .slider-slide.active { opacity: 1; }
    .slider-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); }
    .slider-content { position: relative; z-index: 2; text-align: center; color: #fff; padding: 20px; max-width: 800px; }
    .slider-title { font-size: 48px; font-weight: 700; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); animation: slideUp 0.8s ease-out; }
    .slider-subtitle { font-size: 20px; margin-bottom: 25px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); animation: slideUp 0.8s ease-out 0.2s both; }
    .slider-button { display: inline-block; padding: 15px 35px; background: #c9a55c; color: #fff; text-decoration: none; border-radius: 50px; font-weight: 600; transition: all 0.3s ease; animation: slideUp 0.8s ease-out 0.4s both; }
    .slider-button:hover { background: #b8934a; transform: translateY(-2px); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .slider-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: #fff; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; z-index: 10; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; }
    .slider-nav:hover { background: rgba(255,255,255,0.4); }
    .slider-prev { left: 20px; } .slider-next { right: 20px; }
    .slider-dots { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10; }
    .slider-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; background: transparent; cursor: pointer; transition: all 0.3s ease; }
    .slider-dot.active { background: #c9a55c; border-color: #c9a55c; }
    @media (max-width: 767px) { .church-home-slider { height: 80vh; max-height: 450px; min-height: 350px; } .slider-title { font-size: 26px; } .slider-subtitle { font-size: 14px; } }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.church-home-slider');
        sliders.forEach(function(slider) {
            const slides = slider.querySelectorAll('.slider-slide');
            const dots = slider.querySelectorAll('.slider-dot');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            const autoPlay = slider.dataset.auto === '1';
            const speed = parseInt(slider.dataset.speed) || 5000;
            let currentIndex = 0;
            let autoInterval = null;
            
            function showSlide(index) {
                slides.forEach((slide, i) => { 
                    slide.classList.remove('active'); 
                    dots[i].classList.remove('active'); 
                });
                slides[index].classList.add('active');
                dots[index].classList.add('active');
                currentIndex = index;
            }
            
            function nextSlide() { 
                let newIndex = currentIndex + 1; 
                if (newIndex >= slides.length) newIndex = 0; 
                showSlide(newIndex); 
            }
            
            function prevSlide() { 
                let newIndex = currentIndex - 1; 
                if (newIndex < 0) newIndex = slides.length - 1; 
                showSlide(newIndex); 
            }
            
            function startAutoPlay() { 
                if (autoPlay) { 
                    autoInterval = setInterval(nextSlide, speed); 
                } 
            }
            
            function stopAutoPlay() { 
                if (autoInterval) { 
                    clearInterval(autoInterval); 
                    autoInterval = null; 
                } 
            }
            
            nextBtn.addEventListener('click', function() { stopAutoPlay(); nextSlide(); startAutoPlay(); });
            prevBtn.addEventListener('click', function() { stopAutoPlay(); prevSlide(); startAutoPlay(); });
            dots.forEach(function(dot, index) { 
                dot.addEventListener('click', function() { stopAutoPlay(); showSlide(index); startAutoPlay(); }); 
            });
            slider.addEventListener('mouseenter', stopAutoPlay);
            slider.addEventListener('mouseleave', startAutoPlay);
            startAutoPlay();
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('church_home_slider', 'church_home_slider_shortcode');

// ===== 23. ВИДЖЕТЫ =====
function church_child_widgets_init() {
    register_sidebar(array(
        'name'          => __('Подвал - Колонка 1', 'church'),
        'id'            => 'footer-1',
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => __('Подвал - Колонка 2', 'church'),
        'id'            => 'footer-2',
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'church_child_widgets_init');

// ===== 24. УДАЛЕНИЕ ЛИШНЕГО =====
function church_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'church_cleanup_head');

// ===== 25. БЕЗОПАСНОСТЬ =====
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// ===== 26. ЕДИНАЯ ФУНКЦИЯ CUSTOMIZER (ПОЖЕРТВОВАНИЯ + КОНТАКТЫ + СЛАЙДЕР) =====
function church_customize_register($wp_customize) {
    // === ПОЖЕРТВОВАНИЯ ===
    $wp_customize->add_section('church_donate_settings', array(
        'title'    => __('⛪ Пожертвования', 'church-kadence-child'),
        'priority' => 30
    ));
    
    $wp_customize->add_setting('donate_button_text', array(
        'default'           => 'Пожертвовать',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('donate_button_text', array(
        'label'   => __('Текст кнопки', 'church-kadence-child'),
        'section' => 'church_donate_settings',
        'type'    => 'text'
    ));
    
    $wp_customize->add_setting('donate_button_url', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('donate_button_url', array(
        'label'       => __('Ссылка для пожертвований', 'church-kadence-child'),
        'section'     => 'church_donate_settings',
        'type'        => 'url',
        'description' => __('Укажите ссылку на платежную систему или страницу', 'church-kadence-child')
    ));
    
    $wp_customize->add_setting('donate_show_in_menu', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    $wp_customize->add_control('donate_show_in_menu', array(
        'label'   => __('Показывать кнопку в шапке', 'church-kadence-child'),
        'section' => 'church_donate_settings',
        'type'    => 'checkbox'
    ));
    
    // === КОНТАКТЫ ===
    $wp_customize->add_section('church_contacts_settings', array(
        'title'    => __('📍 Контакты', 'church-kadence-child'),
        'priority' => 39
    ));
    
    $wp_customize->add_setting('contacts_address', array(
        'default'           => 'г. Краснодар, ул. Красная, 10',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_address', array(
        'label'   => __('Адрес храма', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'text'
    ));
    
    $wp_customize->add_setting('contacts_phone', array(
        'default'           => '+7 (861) 123-45-67',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_phone', array(
        'label'   => __('Телефон', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'text'
    ));
    
    $wp_customize->add_setting('contacts_email', array(
        'default'           => 'info@alexander-nevskiysobor.ru',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_email', array(
        'label'   => __('Email', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'email'
    ));
    
    $wp_customize->add_setting('contacts_schedule', array(
        'default'           => 'Пн-Вс: 08:00 - 20:00',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_schedule', array(
        'label'   => __('Режим работы', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'text'
    ));
    
    $wp_customize->add_setting('contacts_text', array(
        'default'           => 'Мы всегда рады видеть вас в нашем храме.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_text', array(
        'label'   => __('Текст на странице контактов', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'textarea'
    ));
    
    $wp_customize->add_setting('contacts_map_iframe', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh'
    ));
    $wp_customize->add_control('contacts_map_iframe', array(
        'label'       => __('Код iframe Яндекс.Карты', 'church-kadence-child'),
        'section'     => 'church_contacts_settings',
        'type'        => 'textarea',
        'description' => __('Вставьте код iframe из Яндекс.Карт', 'church-kadence-child')
    ));
    
    $wp_customize->add_setting('contacts_show_map', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    $wp_customize->add_control('contacts_show_map', array(
        'label'   => __('Показывать карту на странице', 'church-kadence-child'),
        'section' => 'church_contacts_settings',
        'type'    => 'checkbox'
    ));
    
    // === СЛАЙДЕР ===
    $wp_customize->add_section('church_home_slider', array(
        'title'    => __('🖼️ Слайдер на главной', 'church-kadence-child'),
        'priority' => 25
    ));
    
    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_setting("slider_image_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize,
            "slider_image_{$i}",
            array(
                'label'    => sprintf(__('Изображение %d', 'church-kadence-child'), $i),
                'section'  => 'church_home_slider',
                'settings' => "slider_image_{$i}"
            )
        ));
        
        $wp_customize->add_setting("slider_title_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh'
        ));
        $wp_customize->add_control("slider_title_{$i}", array(
            'label'   => sprintf(__('Заголовок %d', 'church-kadence-child'), $i),
            'section' => 'church_home_slider',
            'settings'=> "slider_title_{$i}",
            'type'    => 'text'
        ));
        
        $wp_customize->add_setting("slider_subtitle_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh'
        ));
        $wp_customize->add_control("slider_subtitle_{$i}", array(
            'label'   => sprintf(__('Подзаголовок %d', 'church-kadence-child'), $i),
            'section' => 'church_home_slider',
            'settings'=> "slider_subtitle_{$i}",
            'type'    => 'text'
        ));
        
        $wp_customize->add_setting("slider_link_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        ));
        $wp_customize->add_control("slider_link_{$i}", array(
            'label'   => sprintf(__('Ссылка %d', 'church-kadence-child'), $i),
            'section' => 'church_home_slider',
            'settings'=> "slider_link_{$i}",
            'type'    => 'url'
        ));
    }
    
    $wp_customize->add_setting('slider_auto_play', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    $wp_customize->add_control('slider_auto_play', array(
        'label'   => __('Автовоспроизведение', 'church-kadence-child'),
        'section' => 'church_home_slider',
        'type'    => 'checkbox'
    ));
    
    $wp_customize->add_setting('slider_transition_speed', array(
        'default'           => 5000,
        'sanitize_callback' => 'absint'
    ));
    $wp_customize->add_control('slider_transition_speed', array(
        'label'      => __('Скорость перехода (мс)', 'church-kadence-child'),
        'section'    => 'church_home_slider',
        'type'       => 'number',
        'input_attrs'=> array('min' => 2000, 'max' => 10000, 'step' => 500)
    ));
}
add_action('customize_register', 'church_customize_register', 20);

// ===== 27. ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ =====
function church_get_donate_settings() {
    return array(
        'text' => get_theme_mod('donate_button_text', 'Пожертвовать'),
        'url'  => get_theme_mod('donate_button_url', '#'),
        'show' => get_theme_mod('donate_show_in_menu', true)
    );
}

function church_get_contacts_settings() {
    return array(
        'address'  => get_theme_mod('contacts_address', 'г. Краснодар, ул. Красная, 10'),
        'phone'    => get_theme_mod('contacts_phone', '+7 (861) 123-45-67'),
        'email'    => get_theme_mod('contacts_email', 'info@alexander-nevskiysobor.ru'),
        'schedule' => get_theme_mod('contacts_schedule', 'Пн-Вс: 08:00 - 20:00'),
        'text'     => get_theme_mod('contacts_text', ''),
        'map'      => get_theme_mod('contacts_map_iframe', ''),
        'show_map' => get_theme_mod('contacts_show_map', true)
    );
}

function church_get_slider_settings() {
    $slides = array();
    for ($i = 1; $i <= 5; $i++) {
        $image = get_theme_mod("slider_image_{$i}", '');
        if (!empty($image)) {
            $slides[] = array(
                'image'    => $image,
                'title'    => get_theme_mod("slider_title_{$i}", ''),
                'subtitle' => get_theme_mod("slider_subtitle_{$i}", ''),
                'link'     => get_theme_mod("slider_link_{$i}", '')
            );
        }
    }
    return array(
        'slides' => $slides,
        'auto'   => get_theme_mod('slider_auto_play', true),
        'speed'  => get_theme_mod('slider_transition_speed', 5000)
    );
}

// ===== 28. ДОБАВЛЕНИЕ КНОПКИ ПОЖЕРТВОВАНИЙ В МЕНЮ =====
function church_add_donate_to_menu($items, $args) {
    if ($args->theme_location === 'primary') {
        $donate = church_get_donate_settings();
        if ($donate['show'] && !empty($donate['url'])) {
            $items .= '<li class="menu-item donate-menu-item">';
            $items .= '<a href="' . esc_url($donate['url']) . '" class="donate-button">' . esc_html($donate['text']) . '</a>';
            $items .= '</li>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'church_add_donate_to_menu', 10, 2);

function church_add_menu_classes($classes, $item, $args) {
    if ($args->theme_location === 'primary' && in_array('menu-item-has-children', $classes)) {
        $classes[] = 'has-dropdown';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'church_add_menu_classes', 10, 3);

// ===== 29. КОЛОНКИ В СПИСКЕ ЗАКАЗОВ =====
function church_order_columns($columns) {
    $new_columns = array(
        'cb'       => (isset($columns['cb']) ? $columns['cb'] : '<input type="checkbox" />'),
        'order_id' => '№ Заказа',
        'title'    => 'Услуга',
        'customer' => 'Клиент',
        'amount'   => 'Сумма',
        'date'     => 'Дата'
    );
    return $new_columns;
}
add_filter('manage_church_order_posts_columns', 'church_order_columns');

function church_order_custom_column($column, $post_id) {
    switch ($column) {
        case 'order_id':
            echo '#' . $post_id;
            break;
        case 'customer':
            echo esc_html(get_post_meta($post_id, '_customer_name', true)) . 
                 '<br><small>' . esc_html(get_post_meta($post_id, '_customer_phone', true)) . '</small>';
            break;
        case 'amount':
            echo '<strong>' . esc_html(get_post_meta($post_id, '_total_amount', true)) . ' руб.</strong>';
            break;
    }
}
add_action('manage_church_order_posts_custom_column', 'church_order_custom_column', 10, 2);

// ===== 30. СБРОС ПРАВИЛ ПЕРЕЗАПИСИ =====
function church_flush_rewrite_rules_on_activation() {
    church_register_services_cpt();
    church_register_news_cpt();
    church_register_history_cpt();
    church_register_orders_cpt();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'church_flush_rewrite_rules_on_activation');

// ===== 31. КНОПКА СБРОСА ПРАВИЛ В АДМИНКЕ =====
function church_add_rewrite_flush_button() {
    add_submenu_page(
        'church-payments',
        'Сброс правил',
        '🔄 Сброс URL',
        'manage_options',
        'church-rewrite-flush',
        'church_rewrite_flush_html'
    );
}
add_action('admin_menu', 'church_add_rewrite_flush_button');

function church_rewrite_flush_html() {
    if (isset($_POST['church_flush_rewrite'])) {
        check_admin_referer('church_flush_rewrite_nonce', 'church_flush_rewrite_nonce');
        church_register_services_cpt();
        church_register_news_cpt();
        church_register_history_cpt();
        flush_rewrite_rules();
        echo '<div class="notice notice-success"><p>✅ Правила перезаписи сброшены!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>🔄 Сброс правил перезаписи URL</h1>
        <p>Если страницы новостей, услуг или истории не открываются (404 ошибка), нажмите кнопку ниже.</p>
        <form method="post">
            <?php wp_nonce_field('church_flush_rewrite_nonce', 'church_flush_rewrite_nonce'); ?>
            <?php submit_button('Сбросить правила', 'primary', 'church_flush_rewrite'); ?>
        </form>
    </div>
    <?php
}

// ===== 32. РАЗРЕШИТЬ IFRAME В КОНТАКТАХ =====
function church_allow_iframe_in_contacts($allowedposttags) {
    $allowedposttags['iframe'] = array(
        'src'             => true,
        'height'          => true,
        'width'           => true,
        'frameborder'     => true,
        'allowfullscreen' => true,
        'style'           => true,
        'name'            => true,
        'id'              => true,
        'class'           => true,
        'scrolling'       => true
    );
    return $allowedposttags;
}
add_filter('wp_kses_allowed_html', 'church_allow_iframe_in_contacts', 10, 2);