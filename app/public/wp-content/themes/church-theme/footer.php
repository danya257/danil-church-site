<footer class="site-footer">
    <div class="container">
        <nav class="footer-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'menu_class' => 'footer-menu',
                'container' => false,
                'depth' => 1,
            ));
            ?>
        </nav>
        <p>&copy; 2026 Войсковой собор святого благоверного князя Александра Невского г. Краснодара — Приходской сайт Войскового собора г. Краснодара. Все права защищены.</p>
        <p>Адрес: г. Санкт-Петербург, площадь Александра Невского, 1</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
