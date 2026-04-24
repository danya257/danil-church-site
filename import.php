<?php
require_once('wp-load.php');

// Контакты
$content_contacts = "
<h2>Войсковой собор святого благоверного князя Александра Невского</h2>
<p><strong>Адрес:</strong><br>
Местная религиозная организация православный приход войскового собора святого благоверного князя Александра Невского<br>
Юридический адрес: 350063, Россия, г. Краснодар, ул. Красная, д. 1<br>
Фактический адрес: 350063, Россия, г. Краснодар, ул. Постовая, д. 26</p>
<p><strong>Телефон:</strong><br>
(861) 262-00-20<br>
8-918-315-94-13 (настоятель Собора протоиерей Иоанн Гармаш)<br>
8-953-080-76-72 (киоск, 07:00-19:00)</p>
<p><strong>E-mail:</strong> nevskiy-sobor@mail.ru</p>
<p><strong>Группа ВКОНТАКТЕ:</strong> <a href='https://vk.com/voyskovoysoborkrasnodar'>https://vk.com/voyskovoysoborkrasnodar</a></p>
<p><strong>Телеграмм-канал:</strong> <a href='https://t.me/alexnewsobor'>https://t.me/alexnewsobor</a></p>
<p><strong>Проезд:</strong><br>
Трамвай: 2, 4 – до ост. Городской сад<br>
Троллейбус: 7, 12, 20 – до ост. ул. Советская; 9, 10 – до ост. ул. Постовая<br>
Маршрутное такси: 3, 19Б (49), 26А, 44</p>
";

$page_contacts = array(
    'post_title'    => 'Контакты',
    'post_content'  => $content_contacts,
    'post_status'   => 'publish',
    'post_type'     => 'page',
);

$contacts_id = wp_insert_post($page_contacts);

// Расписание богослужений (предполагаемое, так как не извлечено полностью)
$content_schedule = "
<h2>Расписание богослужений</h2>
<p>Воскресенье:<br>
Ранняя Литургия: 7:00<br>
Поздняя Литургия: 10:00</p>
<p>Будние дни:<br>
Вечерня: 17:00<br>
Утреня: 6:00<br>
Литургия: 7:00</p>
<p>Суббота:<br>
Всенощное бдение: 17:00</p>
";

$page_schedule = array(
    'post_title'    => 'Расписание богослужений',
    'post_content'  => $content_schedule,
    'post_status'   => 'publish',
    'post_type'     => 'page',
);

$schedule_id = wp_insert_post($page_schedule);

// Духовенство
$content_clergy = "
<h2>Духовенство собора</h2>
<h3>Настоятель собора протоиерей Иоанн Гармаш</h3>
<p>Гармаш Иван Васильевич родился 8 февраля 1965 г. в х. Плавни Крымского района Краснодарского края.</p>
<h3>Иерей Вячеслав Феер</h3>
<p>Дата рождения: 17 апреля 1989 г.</p>
<h3>Иерей Александр Клочков</h3>
<p>Образование: Николо-Угрешская Духовная Семинария, Московская Духовная Академия.</p>
<h3>Иерей Олег Попов</h3>
<p>Образование: КубГАУ.</p>
<h3>Протодиакон Максим Кадуров</h3>
<p>Дата рождения: 18.04.1982.</p>
";

$page_clergy = array(
    'post_title'    => 'Духовенство собора',
    'post_content'  => $content_clergy,
    'post_status'   => 'publish',
    'post_type'     => 'page',
);

$clergy_id = wp_insert_post($page_clergy);

// Меню создайте вручную в админке WordPress: Appearance > Menus
$news_category = wp_create_category('Новости');

$news_posts = array(
    array(
        'title' => 'Разговор после поздней литургии',
        'content' => 'В воскресенье после поздней литургии прошёл разговор с отцом Александром. Говорили о воскресном евангельском чтении и житии преподобной Марии Египетской.',
    ),
    array(
        'title' => 'Воскресное пение в радость Господу',
        'content' => 'После Божественной литургии молодёжный хор собрался на репетицию.',
    ),
    // Добавить больше
);

foreach ($news_posts as $post_data) {
    $post = array(
        'post_title'    => $post_data['title'],
        'post_content'  => $post_data['content'],
        'post_status'   => 'publish',
        'post_category' => array($news_category),
    );
    wp_insert_post($post);
}

echo "Content imported successfully.";
?>