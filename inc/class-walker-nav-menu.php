<?php
/**
 * Custom Walker for Church Menu with dropdown support
 */

if (!class_exists('Church_Walker_Nav_Menu')):
class Church_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    // Начало уровня меню
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $submenu_class = ($depth === 0) ? 'sub-menu dropdown' : 'sub-menu nested';
        $output .= "\n$indent<ul class=\"$submenu_class\" role=\"menu\">\n";
    }
    
    // Конец уровня меню
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    // Начало элемента меню
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        // Классы для элемента
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item';
        $classes[] = 'menu-item-depth-' . $depth;
        
        // Активный элемент
        if (in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes)) {
            $classes[] = 'active';
        }
        
        // Добавляем класс для элементов с подменю
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-children';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        // ID элемента
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        // Отступ для вложенных уровней
        $output .= $indent . '<li' . $id . $class_names . ' role="menuitem">';
        
        // Атрибуты ссылки
        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';
        
        // Добавляем классы к ссылке
        $link_classes = 'menu-link';
        if (in_array('menu-item-has-children', $classes)) {
            $link_classes .= ' has-dropdown-toggle';
        }
        if (in_array('current-menu-item', $classes)) {
            $link_classes .= ' current';
        }
        $atts['class'] = $link_classes;
        
        // Формируем атрибуты
        $atts_output = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $atts_output .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        // Текст ссылки
        $item_output = $args->before ?? '';
        $item_output .= '<a' . $atts_output . ' role="menuitem">';
        $item_output .= $args->link_before ?? '';
        $item_output .= apply_filters('the_title', $item->title, $item->ID);
        
        // Стрелочка убрана
        
        $item_output .= $args->link_after ?? '';
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    // Конец элемента меню
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
endif;
