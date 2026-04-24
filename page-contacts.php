<?php
/**
 * Template Name: Страница контактов
 */
get_header(); ?>

<section class="page-header" style="background: linear-gradient(135deg, var(--color-primary), #2d2d44); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; color: beige; margin-bottom: 15px; font-family: 'Playfair Display', serif;"><?php the_title(); ?></h1>
    </div>
</section>

<main class="site-main" style="padding:60px 0;">
    <div class="contacts-page" style="max-width:900px;margin:0 auto;padding:0 20px;">

        <div style="display:flex;gap:30px;align-items:flex-start;margin-bottom:40px;flex-wrap:wrap;">

            <!-- Фото + Проезд слева -->
            <div style="flex:0 0 380px;">
                <img src="/church/wp-content/uploads/sobor_contact.jpg"
                     alt="Войсковой собор"
                     style="width:100%;border-radius:8px;display:block;">
                <p style="text-align:center;font-style:italic;margin-top:10px;font-size:14px;color:#555;">
                    Войсковой собор святого благоверного князя Александра Невского
                </p>
                <div style="margin-top:15px;line-height:2;">
                    <p style="margin:0 0 5px;"><strong>Проезд:</strong></p>
                    <p style="margin:0 0 5px;">Трамвай: 2, 4 — до ост. Городской сад</p>
                    <p style="margin:0 0 5px;">Троллейбус: 7, 12, 20 — до ост. ул. Советская; 9, 10 — до ост. ул. Постовая</p>
                    <p style="margin:0;">Маршрутное такси: 3, 19Б (49), 26А, 44</p>
                </div>
            </div>

            <!-- Контакты справа -->
            <div style="flex:1;min-width:280px;line-height:2;">
                <p><strong>Местная религиозная организация православный приход войскового собора святого благоверного князя Александра Невского</strong></p>

                <p><strong>Юридический адрес:</strong> 350063, Россия, г. Краснодар, ул. Красная, д. 1</p>

                <p><strong>Фактический адрес:</strong> 350063, Россия, г. Краснодар, ул. Постовая, д. 26</p>

                <p><strong>Тел.:</strong> <a href="tel:+78612620020">(861) 262-00-20</a></p>

                <p><a href="tel:+79183159413">8-918-315-94-13</a> (настоятель Собора протоиерей Иоанн Гармаш)</p>

                <p><a href="tel:+79530807672">8-953-080-76-72</a> (киоск, 07:00–19:00)</p>

                <p><strong>e-mail:</strong> <a href="mailto:nevskiy-sobor@mail.ru">nevskiy-sobor@mail.ru</a></p>

                <p><strong>группа ВКОНТАКТЕ:</strong> <a href="https://vk.com/voyskovoysoborkrasnodar" target="_blank">https://vk.com/voyskovoysoborkrasnodar</a></p>

                <p><strong>Telegram:</strong> <a href="https://t.me/alexnewsobor" target="_blank">https://t.me/alexnewsobor</a></p>
            </div>

        </div>

        <!-- Карта -->
        <div style="border-radius:12px;overflow:hidden;">
            <iframe
                src="https://yandex.ru/map-widget/v1/?ll=38.967049,45.014422&spn=0.01,0.01&z=16&pt=38.967049,45.014422,pm2rdm"
                width="100%" height="400" frameborder="0" allowfullscreen="true"
                style="display:block;">
            </iframe>
        </div>

    </div>
</main>

<?php get_footer(); ?>
