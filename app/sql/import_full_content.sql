-- SQL файл для импорта контента с alexander-nevskiysobor.ru
-- Сгенерировано: 2026-04-19 19:02:40
-- Импорт страниц

-- Очистка старых данных (если нужно)
-- DELETE FROM wp_posts WHERE post_type IN ('page', 'post');


-- Главная страница
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
(100, 1, NOW(), NOW(), '
        <div class=\"hero\">
            <div class=\"hero-content\">
                <h1 class=\"hero-title gold-shine\">Войсковой собор святого благоверного князя Александра Невского</h1>
                <p class=\"hero-subtitle\">Православный приход в городе Краснодар</p>
                <a href=\"#raspisanie\" class=\"hero-button\">Расписание богослужений</a>
                <a href=\"#contacts\" class=\"hero-button-outline\">Контакты</a>
            </div>
        </div>
        
        <section class=\"cta-section scroll-reveal\">
            <div class=\"container\">
                <h2>Добро пожаловать в наш храм</h2>
                <p></p>
            </div>
        </section>
        ', 'Главная', '', 'publish', 'closed', 'closed', '', 'index', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);


-- Страница Расписание богослужений
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
(101, 1, NOW(), NOW(), '<h2>Расписание богослужений</h2><p><img alt=\"\" class=\"alignnone size-full wp-image-17251\" decoding=\"async\" fetchpriority=\"high\" height=\"1686\" sizes=\"(max-width: 1200px) 100vw, 1200px\" src=\"https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c.webp\" srcset=\"https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c.webp 1200w, https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c-214x300.webp 214w, https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c-729x1024.webp 729w, https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c-768x1079.webp 768w, https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c-1093x1536.webp 1093w, https://alexander-nevskiysobor.ru/wp-content/uploads/2026/03/0de0304f-55a1-42f1-9298-2429c6920b8c-107x150.webp 107w\" width=\"1200\"/></p>
', 'Расписание богослужений', '', 'publish', 'closed', 'closed', '', 'raspisanie-bogosluzhenij', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);


-- Страница Духовенство
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
(102, 1, NOW(), NOW(), '<h2>Духовенство собора</h2><div class=''clergy-grid''>
            <div class=''feature-card scroll-reveal''>
                <img src=''https://alexander-nevskiysobor.ru/wp-content/uploads/2025/01/IMG_20250115_094503_360-350x200.jpg'' alt=''Иерей Александр Клочков'' class=''clergy-photo''>
                <h3>Иерей Александр Клочков</h3>
                <p class=''position''></p>
                <p>-ФИО: Клочков Александр Владимирович -Образование: Николо-Угрешская Духовная Семинария, Московская Духовная Академия — Дата хиротоний: Диаконская — 06.08.2009: Священническая — 16.08.2009 — Награды:Набедренник, камилавка, наперсный крест -Служение: 2009 г. — Свято-Троицкий Собор г. Краснодара; 2010 г. — Свято-Екатерининский Кафедральный Собор; 2024 г. — Войсковой собор святого благоверного великого князя Александра Невского.</p>
            </div>
            
            <div class=''feature-card scroll-reveal''>
                <img src=''https://alexander-nevskiysobor.ru/wp-content/uploads/2025/01/IMG_20250115_093952_406-1-350x200.jpg'' alt=''Иерей  Олег Попов'' class=''clergy-photo''>
                <h3>Иерей  Олег Попов</h3>
                <p class=''position''></p>
                <p>ФИО: Попов Олег Александрович Образование: 2001г. окончил КубГАУ Должность: Штатный клирик Дата хиротоний: диаконская: 6 августа 2023г.; иерейская: 3 марта 2024 Служение: с 2023 клирик Войскового собора святого благоверного великого князя Александра Невского г. Краснодара</p>
            </div>
            
            <div class=''feature-card scroll-reveal''>
                <img src=''https://alexander-nevskiysobor.ru/wp-content/uploads/2019/11/-МАКСИМ-ДМИТРИЕВИЧ-ПРОТОДИАКОН-1-e1709713712636-350x200.jpg'' alt=''Протодиакон Максим Кадуров'' class=''clergy-photo''>
                <h3>Протодиакон Максим Кадуров</h3>
                <p class=''position''></p>
                <p>ФИО: протодиакон Максим Кадуров Дата рождения: 18.04.1982 Хиротония: 15.02.2006 г. сан диакона Образование: Тобольская Духовная Семинария, Алтайская Государственная Академия Культуры и Искусства на факультет Народно- художественной Культуры на отделение: «Дирижёр Академического хора» и «преподаватель Народно-Художественного Творчества». Институт дополнительного образования АлтГАКИ по специализации: «Преподаватель сольного пения. Камерный певец».  Награды: 5 мая…</p>
            </div>
            
            <div class=''feature-card scroll-reveal''>
                <img src=''https://alexander-nevskiysobor.ru/wp-content/uploads/2019/02/NanxmllXD0I-e1550168147399-350x200.jpg'' alt=''Настоятель собора протоиерей Иоанн Гармаш'' class=''clergy-photo''>
                <h3>Настоятель собора протоиерей Иоанн Гармаш</h3>
                <p class=''position''></p>
                <p>Гармаш Иван Васильевич родился 8 февраля 1965 г. в х. Плавни Крымского района Краснодарского края. Крещен 16 февраля 1965 г. в Свято-Михайло-Архангельском храме г. Крымска. В 1997-2000 гг. учился в Киевской Духовной Семинарии, после чего в 2003 году закончил Киевскую Духовную Академию (защитил дипломную работу по теме: «Исторический обзор римско-католического…</p>
            </div>
            
            <div class=''feature-card scroll-reveal''>
                <img src=''https://alexander-nevskiysobor.ru/wp-content/uploads/2019/02/DSC_3952-350x200.jpg'' alt=''Иерей Вячеслав Феер'' class=''clergy-photo''>
                <h3>Иерей Вячеслав Феер</h3>
                <p class=''position''></p>
                <p>— ФИО: иерей Вячеслав Феер — Образование: Екатеринодарская Духовная семинария — Должность: Штатный священник — Дата рождения: 17 апреля 1989 г. — Дата хиротоний: Диаконская – 31 августа 2011 г., Иерейская – 1 мая 2012 г. Награды: набедренник 2012 г камилавка 2014 г. наперсный крест 2017 г. Служение: 31 августа 2011 года был рукоположен…</p>
            </div>
            </div>', 'Духовенство собора', '', 'publish', 'closed', 'closed', '', 'duhovenstvo', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);


-- Страница Контакты
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
(103, 1, NOW(), NOW(), '
        <div class=\"contacts-section\">
            <h2>Контакты</h2>
            <div class=\"contacts-grid\">
                <div class=\"contacts-info\">
                    <h3>Войсковой собор святого благоверного князя Александра Невского</h3>
                    
                    <h4>Адрес:</h4>
                    <p><strong>Адрес:</strong></p>
<p>Местная религиозная организация православный<br/>
приход войскового собора святого благоверного князя<br/>
Александра Невского<br/>
<strong>Юридический адрес:</strong> 350063, Россия, г. Краснодар, ул. Красная, д. 1<br/>
<span style=\\\"font-family: georgia, serif;\\\"><strong>Фактический адрес:</strong> 350063, Россия, г. Краснодар,<br/>
ул. Постовая, д. 26</span><br/>
<strong>тел.:</strong> (861) 262-00-20,</p>
<p><strong>Проезд:</strong><br/>
<u>Трамвай:</u> 2, 4 – до ост. Городской сад<br/>
<u>Троллейбус:</u> 7, 12, 20 – до ост. ул. Советская<br/>
9, 10 – до ост. ул. Постовая<br/>
<u>Маршрутное такси:</u> 3, 19Б (49), 26А, 44</p>
<p>[su_gmap address=»ул. Постовая, 26, Краснодар, Краснодарский край, 350063″ zoom=»15″]</p>

                    
                    <h4>Телефоны:</h4>
                    <p>(861) 262-00-20<br>8-918-315-94-13 (<br>)
8-953-080-76-72 (</p>
                    
                    <h4>E-mail:</h4>
                    <p><a href=\"mailto:\"></a></p>
                    
                    <h4>Социальные сети:</h4>
                    <p>
                        <a href=\"https://vk.com/voyskovoysoborkrasnodar\">ВКонтакте</a>
                        
                    </p>
                    
                    <h4>Проезд:</h4>
                    <p>Трамвай: 2, 4 – до ост. Городской сад<br>
                    Троллейбус: 7, 12, 20 – до ост. ул. Советская; 9, 10 – до ост. ул. Постовая<br>
                    Маршрутное такси: 3, 19Б (49), 26А, 44</p>
                </div>
                
                <div class=\"contacts-map\">
                    <iframe src=\"https://yandex.ru/map-widget/v1/?ll=38.986755%2C45.032469&z=17&pt=38.986755,45.032469\" width=\"100%\" height=\"450\" frameborder=\"0\" allowfullscreen=\"true\"></iframe>
                </div>
            </div>
        </div>
        ', 'Контакты', '', 'publish', 'closed', 'closed', '', 'kontakty', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);


-- Страница История храма
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
(104, 1, NOW(), NOW(), '<h2>История храма</h2><p><a href=\"\">Летопись собора</a></p>
<p><a href=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/01/4_1-2.jpg\"><img alt=\"\" decoding=\"async\" height=\"129\" src=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/01/4_1-2-300x213.jpg\" width=\"182\"/></a><a href=\"http://new.alexander-nevskiysobor.ru/?cat=13\">История строительства и возрождения Войскового собора святого благоверного князя Александра Невского</a></p>
<p> <a href=\"\">Житие святого благоверного князя Александра Невского</a></p>
<p><a href=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/Алесандр-Невский.jpg\"><img alt=\"\" decoding=\"async\" height=\"140\" src=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/Алесандр-Невский-300x231.jpg\" width=\"182\"/></a><a href=\"http://new.alexander-nevskiysobor.ru/?page_id=59\">Житие небесного покровителя собора – святого благоверного князя Александра Невского</a></p>
<p> <a href=\"\">Казанская икона Пресвятой Богородицы</a></p>
<p><a href=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/23-3.jpg\"><img alt=\"\" decoding=\"async\" height=\"199\" src=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/23-3-266x300.jpg\" width=\"176\"/></a><a href=\"http://new.alexander-nevskiysobor.ru/?page_id=83\">История особо чтимой святыни войскового собора св. благ. кн. Александра Невского г. Краснодара обновленной иконы Божией Матери, именуемой Казанской</a></p>
<p> <a href=\"\">Библиотека</a></p>
<p><a href=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/IMG_1345.jpg\"><img alt=\"\" decoding=\"async\" height=\"116\" loading=\"lazy\" src=\"http://new.alexander-nevskiysobor.ru/wp-content/uploads/2019/02/IMG_1345-300x200.jpg\" width=\"175\"/></a><a href=\"http://new.alexander-nevskiysobor.ru/?page_id=176\">В Войсковом соборе св. благ. кн. Александра Невского действует библиотека.</a></p>
', 'История храма', '', 'publish', 'closed', 'closed', '', 'istoriya-hrama', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);



-- Настройка меню (создайте вручную в админке или используйте этот SQL)


-- Пример создания меню (требует дополнительной настройки в админке)
-- Appearance > Menus > Создать меню "Главное меню"
-- Добавить страницы: Главная, Расписание богослужений, Духовенство, Контакты, История
