<?php
/**
 * Footer template for Church Kadence Child
 */
?>
</main><!-- #content -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Все права защищены.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?> 

<script>
// Фикс мобильного меню — скролл в начало при открытии
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.querySelector('.mobile-toggle');
    if (!toggle) return;
    
    // Перехватываем клик раньше основного обработчика
    toggle.addEventListener('click', function() {
        window.scrollTo(0, 0);
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
    }, true); // true = capture phase, срабатывает первым
});
</script>
</body>
</html>