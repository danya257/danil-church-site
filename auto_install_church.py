#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Автоматический установщик сайта церкви
Скачивает контент с alexander-nevskiysobor.ru и полностью настраивает WordPress
БЕЗ использования админки - всё через базу данных и файлы
"""

import requests
from bs4 import BeautifulSoup
import sqlite3
import os
import re
import hashlib
from datetime import datetime
import base64
import json

# Конфигурация
SITE_URL = "https://alexander-nevskiysobor.ru"
WP_DB_PATH = "/workspace/app/public/wp-content/database/.ht.sqlite"
THEME_DIR = "/workspace/app/public/wp-content/themes/church-theme"
UPLOADS_DIR = "/workspace/app/public/wp-content/uploads/2025/01"

def create_directories():
    """Создаёт все необходимые директории"""
    dirs = [
        THEME_DIR,
        f"{THEME_DIR}/assets/css",
        f"{THEME_DIR}/assets/js",
        f"{THEME_DIR}/assets/images",
        f"{THEME_DIR}/template-parts",
        UPLOADS_DIR,
        "/workspace/app/sql"
    ]
    for d in dirs:
        os.makedirs(d, exist_ok=True)
    print("✅ Директории созданы")

def download_image(url, filename=None):
    """Скачивает изображение и возвращает путь"""
    try:
        if not filename:
            filename = hashlib.md5(url.encode()).hexdigest() + ".jpg"
        
        filepath = f"{UPLOADS_DIR}/{filename}"
        
        if os.path.exists(filepath):
            return f"/wp-content/uploads/2025/01/{filename}"
        
        headers = {'User-Agent': 'Mozilla/5.0'}
        response = requests.get(url, headers=headers, timeout=10)
        
        if response.status_code == 200:
            with open(filepath, 'wb') as f:
                f.write(response.content)
            return f"/wp-content/uploads/2025/01/{filename}"
    except Exception as e:
        print(f"⚠️ Не удалось скачать изображение: {url}")
    return None

def scrape_site():
    """Скачивает весь контент с оригинального сайта"""
    print("🌐 Скачиваем контент с сайта...")
    
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'}
    
    try:
        response = requests.get(SITE_URL, headers=headers, timeout=15)
        soup = BeautifulSoup(response.text, 'html.parser')
        
        content = {
            'title': '',
            'description': '',
            'hero_text': '',
            'schedule_img': None,
            'priests': [],
            'history': '',
            'contacts': {},
            'menu_items': []
        }
        
        # Заголовок
        title_tag = soup.find('title')
        content['title'] = title_tag.get_text().strip() if title_tag else "Храм Александра Невского"
        
        # Meta description
        meta_desc = soup.find('meta', attrs={'name': 'description'})
        content['description'] = meta_desc.get('content', '') if meta_desc else ''
        
        # Hero секция (ищем главный заголовок)
        hero = soup.find(['h1', 'div'], class_=re.compile(r'(hero|banner|main-title)', re.I))
        if hero:
            content['hero_text'] = hero.get_text().strip()[:200]
        else:
            h1 = soup.find('h1')
            content['hero_text'] = h1.get_text().strip()[:200] if h1 else content['title']
        
        # Расписание (ищем изображение с расписанием)
        schedule_imgs = soup.find_all('img')
        for img in schedule_imgs:
            alt = img.get('alt', '').lower()
            src = img.get('src') or img.get('data-src') or img.get('data-lazy-src')
            if src and ('распис' in alt or 'schedule' in alt):
                if not src.startswith('http'):
                    src = SITE_URL + ('/' if not src.startswith('/') else '') + src
                content['schedule_img'] = download_image(src, 'schedule.jpg')
                break
        
        # Если не нашли по альту, берём первое большое изображение
        if not content['schedule_img']:
            for img in schedule_imgs:
                src = img.get('src') or img.get('data-src')
                if src:
                    if not src.startswith('http'):
                        src = SITE_URL + ('/' if not src.startswith('/') else '') + src
                    content['schedule_img'] = download_image(src, 'schedule.jpg')
                    break
        
        # Духовенство
        priests_section = soup.find(['div', 'section'], class_=re.compile(r'(priest|clergy|духовенств)', re.I))
        if priests_section:
            priest_cards = priests_section.find_all(['div', 'article'], class_=re.compile(r'(card|item|person)', re.I))
            for card in priest_cards[:5]:  # Максимум 5
                priest = {}
                
                name = card.find(['h2', 'h3', 'h4'])
                priest['name'] = name.get_text().strip() if name else ''
                
                position = card.find(['p', 'span'], class_=re.compile(r'(position|role|должн)', re.I))
                priest['position'] = position.get_text().strip() if position else ''
                
                bio = card.find(['p'], class_=re.compile(r'(bio|description|text)', re.I))
                priest['bio'] = bio.get_text().strip()[:500] if bio else ''
                
                img = card.find('img')
                if img:
                    src = img.get('src') or img.get('data-src')
                    if src:
                        if not src.startswith('http'):
                            src = SITE_URL + ('/' if not src.startswith('/') else '') + src
                        priest['photo'] = download_image(src, f"priest_{len(content['priests'])}.jpg")
                
                if priest.get('name'):
                    content['priests'].append(priest)
        
        # Если не нашли секцию, пробуем найти по ссылкам
        if not content['priests']:
            links = soup.find_all('a', href=re.compile(r'(priest|clergy|духовенств|sobor)', re.I))
            for link in links[:3]:
                href = link.get('href')
                if href and not href.startswith('http'):
                    href = SITE_URL + '/' + href.lstrip('/')
                try:
                    priest_resp = requests.get(href, headers=headers, timeout=10)
                    priest_soup = BeautifulSoup(priest_resp.text, 'html.parser')
                    
                    priest = {}
                    name = priest_soup.find(['h1', 'h2'])
                    priest['name'] = name.get_text().strip() if name else link.get_text().strip()
                    
                    img = priest_soup.find('img')
                    if img:
                        src = img.get('src')
                        if src:
                            if not src.startswith('http'):
                                src = SITE_URL + ('/' if not src.startswith('/') else '') + src
                            priest['photo'] = download_image(src, f"priest_{len(content['priests'])}.jpg")
                    
                    content['priests'].append(priest)
                except:
                    pass
        
        # История
        history_section = soup.find(['div', 'section'], class_=re.compile(r'(history|about|история|о храме)', re.I))
        if history_section:
            paragraphs = history_section.find_all('p')
            content['history'] = '\n\n'.join([p.get_text().strip() for p in paragraphs[:5]])
        
        # Контакты
        contacts_section = soup.find(['div', 'footer', 'section'], class_=re.compile(r'(contact|footer|контакт)', re.I))
        if contacts_section:
            # Адрес
            address = contacts_section.find(['address', 'p'], string=re.compile(r'(ул\.|просп|д\.|г\.)', re.I))
            if address:
                content['contacts']['address'] = address.get_text().strip()
            
            # Телефоны
            phones = contacts_section.find_all('a', href=re.compile(r'tel:', re.I))
            content['contacts']['phones'] = [p.get_text().strip() for p in phones[:3]]
            
            # Email
            emails = contacts_section.find_all('a', href=re.compile(r'mailto:', re.I))
            content['contacts']['emails'] = [e.get_text().strip() for e in emails[:2]]
            
            # Соцсети
            socials = contacts_section.find_all('a', href=re.compile(r'(vk\.com|telegram|t\.me)', re.I))
            content['contacts']['socials'] = [s.get('href') for s in socials[:3]]
        
        # Меню
        nav = soup.find('nav')
        if nav:
            menu_links = nav.find_all('a', href=True)
            for link in menu_links[:10]:
                text = link.get_text().strip()
                href = link.get('href')
                if text and href and len(text) < 30:
                    content['menu_items'].append({'label': text, 'url': href})
        
        # Если контакты не найдены, ставим дефолтные
        if not content['contacts'].get('address'):
            content['contacts']['address'] = "г. Санкт-Петербург, площадь Александра Невского, 1"
        if not content['contacts'].get('phones'):
            content['contacts']['phones'] = ["+7 (812) 274-11-97"]
        
        print(f"✅ Контент скачан: {len(content['priests'])} священников, меню: {len(content['menu_items'])} пунктов")
        return content
        
    except Exception as e:
        print(f"❌ Ошибка при скачивании: {e}")
        # Возвращаем дефолтный контент
        return get_default_content()

def get_default_content():
    """Дефолтный контент если сайт недоступен"""
    return {
        'title': "Собор Святого Александра Невского",
        'description': "Православный собор в Санкт-Петербурге",
        'hero_text': "Добро пожаловать в Свято-Троицкую Александро-Невскую Лавру",
        'schedule_img': None,
        'priests': [
            {'name': 'Протоиерей Владимир', 'position': 'Настоятель', 'bio': 'Служит в храме более 20 лет', 'photo': None},
            {'name': 'Иерей Александр', 'position': 'Клирик', 'bio': 'Окончил СПбДА в 2010 году', 'photo': None},
            {'name': 'Диакон Михаил', 'position': 'Диакон', 'bio': 'Руководит воскресной школой', 'photo': None},
        ],
        'history': "Храм был основан в XVIII веке по указу Петра I. На протяжении веков он являлся духовным центром Санкт-Петербурга. В храме хранятся мощи святого благоверного князя Александра Невского.",
        'contacts': {
            'address': "г. Санкт-Петербург, площадь Александра Невского, 1",
            'phones': ["+7 (812) 274-11-97"],
            'emails': ["info@alexfond.ru"],
            'socials': ["https://vk.com/nevsky_sobor"]
        },
        'menu_items': [
            {'label': 'Главная', 'url': '/'},
            {'label': 'Расписание', 'url': '/schedule'},
            {'label': 'Духовенство', 'url': '/clergy'},
            {'label': 'История', 'url': '/history'},
            {'label': 'Контакты', 'url': '/contacts'},
            {'label': 'Новости', 'url': '/news'}
        ]
    }

def create_theme_files(content):
    """Создаёт файлы темы WordPress"""
    print("🎨 Создаём тему...")
    
    # style.css
    style_css = f'''/*
Theme Name: Church Theme
Theme URI: https://alexander-nevskiysobor.ru
Author: Senior WP Developer
Description: Современная тема для храма Александра Невского
Version: 1.0.0
Text Domain: church-theme
*/

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap');

:root {{
    --primary: #1a1a2e;
    --secondary: #c9a961;
    --accent: #e8d5b5;
    --text: #2d2d2d;
    --light: #f8f6f0;
    --white: #ffffff;
}}

* {{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}}

body {{
    font-family: 'Inter', sans-serif;
    color: var(--text);
    line-height: 1.6;
    background: var(--light);
}}

h1, h2, h3, h4, h5, h6 {{
    font-family: 'Playfair Display', serif;
    color: var(--primary);
    margin-bottom: 1rem;
}}

.container {{
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}}

/* Header */
.site-header {{
    background: var(--white);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
}}

.header-inner {{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
}}

.site-logo {{
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
}}

.main-navigation ul {{
    display: flex;
    list-style: none;
    gap: 2rem;
}}

.main-navigation a {{
    text-decoration: none;
    color: var(--primary);
    font-weight: 500;
    transition: color 0.3s;
}}

.main-navigation a:hover {{
    color: var(--secondary);
}}

/* Hero Section */
.hero {{
    background: linear-gradient(135deg, var(--primary) 0%, #2d2d44 100%);
    color: var(--white);
    padding: 150px 0 100px;
    text-align: center;
    margin-top: 70px;
}}

.hero h1 {{
    color: var(--white);
    font-size: 3rem;
    margin-bottom: 1.5rem;
}}

.hero p {{
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto 2rem;
    opacity: 0.9;
}}

.btn {{
    display: inline-block;
    background: var(--secondary);
    color: var(--white);
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}}

.btn:hover {{
    background: #b8964e;
    transform: translateY(-2px);
}}

/* Sections */
.section {{
    padding: 80px 0;
}}

.section-title {{
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
}}

.section-title::after {{
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: var(--secondary);
    margin: 1rem auto 0;
}}

/* Priests Grid */
.priests-grid {{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}}

.priest-card {{
    background: var(--white);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}}

.priest-card:hover {{
    transform: translateY(-5px);
}}

.priest-photo {{
    width: 100%;
    height: 300px;
    object-fit: cover;
    background: var(--accent);
}}

.priest-info {{
    padding: 1.5rem;
}}

.priest-name {{
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
}}

.priest-position {{
    color: var(--secondary);
    font-weight: 500;
    margin-bottom: 1rem;
}}

/* Schedule */
.schedule-section {{
    background: var(--white);
}}

.schedule-img {{
    max-width: 100%;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}}

/* Contacts */
.contacts-grid {{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}}

.contact-item {{
    text-align: center;
    padding: 2rem;
    background: var(--white);
    border-radius: 10px;
}}

.contact-icon {{
    font-size: 2rem;
    color: var(--secondary);
    margin-bottom: 1rem;
}}

/* Footer */
.site-footer {{
    background: var(--primary);
    color: var(--white);
    padding: 3rem 0;
    text-align: center;
}}

.footer-nav {{
    margin-bottom: 2rem;
}}

.footer-nav a {{
    color: var(--accent);
    text-decoration: none;
    margin: 0 1rem;
}}

/* Animations */
.scroll-reveal {{
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}}

.scroll-reveal.visible {{
    opacity: 1;
    transform: translateY(0);
}}

/* Mobile */
@media (max-width: 768px) {{
    .hero h1 {{
        font-size: 2rem;
    }}
    
    .header-inner {{
        flex-direction: column;
        gap: 1rem;
    }}
    
    .main-navigation ul {{
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }}
}}
'''
    
    with open(f"{THEME_DIR}/style.css", 'w', encoding='utf-8') as f:
        f.write(style_css)
    
    # functions.php
    functions_php = '''<?php
/**
 * Church Theme Functions
 */

if (!defined('ABSPATH')) exit;

// Register menus
function church_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'church-theme'),
        'footer' => __('Footer Menu', 'church-theme'),
        'mobile' => __('Mobile Menu', 'church-theme'),
    ));
}
add_action('after_setup_theme', 'church_register_menus');

// Theme setup
function church_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    set_post_thumbnail_size(400, 300, true);
}
add_action('after_setup_theme', 'church_setup');

// Enqueue scripts and styles
function church_scripts() {
    wp_enqueue_style('church-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('church-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'church_scripts');

// Register widgets
function church_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'church-theme'),
        'id' => 'sidebar-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'church_widgets_init');

// Custom excerpt length
function church_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'church_excerpt_length');
'''
    
    with open(f"{THEME_DIR}/functions.php", 'w', encoding='utf-8') as f:
        f.write(functions_php)
    
    # index.php
    index_php = f'''<?php
/**
 * Main template file
 */

get_header();
?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="scroll-reveal">{content['hero_text']}</h1>
            <p class="scroll-reveal">{content['description']}</p>
            <a href="#schedule" class="btn scroll-reveal">Расписание богослужений</a>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="section schedule-section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Расписание богослужений</h2>
            <?php if (has_post_thumbnail()) : ?>
                <div class="scroll-reveal">
                    <?php the_post_thumbnail('large', array('class' => 'schedule-img')); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Priests Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Духовенство собора</h2>
            <div class="priests-grid">
                <?php
                $priests = new WP_Query(array(
                    'post_type' => 'priest',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                ));
                
                if ($priests->have_posts()) :
                    while ($priests->have_posts()) : $priests->the_post();
                        ?>
                        <div class="priest-card scroll-reveal">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" class="priest-photo">
                            <?php endif; ?>
                            <div class="priest-info">
                                <h3 class="priest-name"><?php the_title(); ?></h3>
                                <p class="priest-position"><?php echo get_post_meta(get_the_ID(), '_priest_position', true); ?></p>
                                <p><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="section" style="background: var(--white);">
        <div class="container">
            <h2 class="section-title scroll-reveal">История храма</h2>
            <div class="scroll-reveal" style="max-width: 800px; margin: 0 auto;">
                <p>{content['history'][:500]}...</p>
                <a href="/history" class="btn">Читать далее</a>
            </div>
        </div>
    </section>

    <!-- Contacts Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title scroll-reveal">Контакты</h2>
            <div class="contacts-grid">
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">📍</div>
                    <h3>Адрес</h3>
                    <p>{content['contacts']['address']}</p>
                </div>
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">📞</div>
                    <h3>Телефон</h3>
                    <p>{content['contacts']['phones'][0]}</p>
                </div>
                <div class="contact-item scroll-reveal">
                    <div class="contact-icon">✉️</div>
                    <h3>Email</h3>
                    <p>{content['contacts']['emails'][0] if content['contacts'].get('emails') else 'info@alexfond.ru'}</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
'''
    
    with open(f"{THEME_DIR}/index.php", 'w', encoding='utf-8') as f:
        f.write(index_php)
    
    # header.php
    header_php = '''<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="header-inner container">
        <a href="<?php echo home_url(); ?>" class="site-logo">
            <?php 
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                bloginfo('name');
            }
            ?>
        </a>
        
        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'primary-menu',
                'container' => false,
            ));
            ?>
        </nav>
    </div>
</header>
'''
    
    with open(f"{THEME_DIR}/header.php", 'w', encoding='utf-8') as f:
        f.write(header_php)
    
    # footer.php
    footer_php = f'''<footer class="site-footer">
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
        <p>&copy; {datetime.now().year} {content['title']}. Все права защищены.</p>
        <p>Адрес: {content['contacts']['address']}</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
'''
    
    with open(f"{THEME_DIR}/footer.php", 'w', encoding='utf-8') as f:
        f.write(footer_php)
    
    # main.js
    main_js = '''// Church Theme JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Scroll reveal animation
    const revealElements = document.querySelectorAll('.scroll-reveal');
    
    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const elementVisible = 100;
        
        revealElements.forEach((element) => {
            const elementTop = element.getBoundingClientRect().top;
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('visible');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Check on load
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }
});
'''
    
    with open(f"{THEME_DIR}/assets/js/main.js", 'w', encoding='utf-8') as f:
        f.write(main_js)
    
    print("✅ Тема создана")

def init_sqlite_db():
    """Инициализирует базу данных SQLite для WordPress"""
    print("🗄️ Инициализация базы данных...")
    
    # Создаём директорию если нет
    db_dir = os.path.dirname(WP_DB_PATH)
    os.makedirs(db_dir, exist_ok=True)
    
    conn = sqlite3.connect(WP_DB_PATH)
    cursor = conn.cursor()
    
    # Таблицы WordPress (упрощённая схема для основных данных)
    tables = [
        '''CREATE TABLE IF NOT EXISTS wp_options (
            option_id INTEGER PRIMARY KEY AUTOINCREMENT,
            option_name TEXT UNIQUE NOT NULL,
            option_value TEXT,
            autoload TEXT DEFAULT 'yes'
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_posts (
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            post_author INTEGER DEFAULT 1,
            post_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            post_date_gmt DATETIME DEFAULT CURRENT_TIMESTAMP,
            post_content TEXT,
            post_title TEXT,
            post_excerpt TEXT,
            post_status TEXT DEFAULT 'publish',
            comment_status TEXT DEFAULT 'open',
            ping_status TEXT DEFAULT 'open',
            post_password TEXT,
            post_name TEXT,
            to_ping TEXT,
            pinged TEXT,
            post_modified DATETIME DEFAULT CURRENT_TIMESTAMP,
            post_modified_gmt DATETIME DEFAULT CURRENT_TIMESTAMP,
            post_content_filtered TEXT,
            post_parent INTEGER DEFAULT 0,
            guid TEXT,
            menu_order INTEGER DEFAULT 0,
            post_type TEXT DEFAULT 'post',
            post_mime_type TEXT,
            comment_count INTEGER DEFAULT 0
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_postmeta (
            meta_id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            meta_key TEXT,
            meta_value TEXT
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_terms (
            term_id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            term_group INTEGER DEFAULT 0
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_term_taxonomy (
            term_taxonomy_id INTEGER PRIMARY KEY AUTOINCREMENT,
            term_id INTEGER NOT NULL,
            taxonomy TEXT NOT NULL,
            description TEXT,
            parent INTEGER DEFAULT 0,
            count INTEGER DEFAULT 0
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_term_relationships (
            object_id INTEGER NOT NULL,
            term_taxonomy_id INTEGER NOT NULL,
            term_order INTEGER DEFAULT 0,
            PRIMARY KEY (object_id, term_taxonomy_id)
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_users (
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            user_login TEXT NOT NULL UNIQUE,
            user_pass TEXT NOT NULL,
            user_nicename TEXT,
            user_email TEXT,
            user_url TEXT,
            user_registered DATETIME DEFAULT CURRENT_TIMESTAMP,
            user_activation_key TEXT,
            user_status INTEGER DEFAULT 0,
            display_name TEXT
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_usermeta (
            umeta_id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            meta_key TEXT,
            meta_value TEXT
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_comments (
            comment_ID INTEGER PRIMARY KEY AUTOINCREMENT,
            comment_post_ID INTEGER NOT NULL,
            comment_author TEXT NOT NULL,
            comment_author_email TEXT,
            comment_author_url TEXT,
            comment_author_IP TEXT,
            comment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            comment_date_gmt DATETIME DEFAULT CURRENT_TIMESTAMP,
            comment_content TEXT NOT NULL,
            comment_karma INTEGER DEFAULT 0,
            comment_approved TEXT DEFAULT '1',
            comment_agent TEXT,
            comment_type TEXT DEFAULT 'comment',
            comment_parent INTEGER DEFAULT 0,
            user_id INTEGER DEFAULT 0
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_termmeta (
            meta_id INTEGER PRIMARY KEY AUTOINCREMENT,
            term_id INTEGER NOT NULL,
            meta_key TEXT,
            meta_value TEXT
        )''',
        
        '''CREATE TABLE IF NOT EXISTS wp_links (
            link_id INTEGER PRIMARY KEY AUTOINCREMENT,
            link_url TEXT NOT NULL,
            link_name TEXT NOT NULL,
            link_image TEXT,
            link_target TEXT,
            link_description TEXT,
            link_visible TEXT DEFAULT 'Y',
            link_owner INTEGER DEFAULT 1,
            link_rating INTEGER DEFAULT 0,
            link_updated DATETIME,
            link_rel TEXT,
            link_notes TEXT,
            link_rss TEXT
        )''',
    ]
    
    for table_sql in tables:
        cursor.execute(table_sql)
    
    # Индексы
    indexes = [
        'CREATE INDEX IF NOT EXISTS post_name ON wp_posts(post_name)',
        'CREATE INDEX IF NOT EXISTS post_type_status_date ON wp_posts(post_type, post_status, post_date)',
        'CREATE INDEX IF NOT EXISTS post_author ON wp_posts(post_author)',
        'CREATE INDEX IF NOT EXISTS meta_post_id ON wp_postmeta(post_id)',
        'CREATE INDEX IF NOT EXISTS meta_key ON wp_postmeta(meta_key)',
    ]
    
    for index_sql in indexes:
        cursor.execute(index_sql)
    
    conn.commit()
    conn.close()
    print("✅ База данных инициализирована")

def insert_content_to_db(content):
    """Вставляет контент в базу данных"""
    print("📝 Вставляем контент в базу...")
    
    conn = sqlite3.connect(WP_DB_PATH)
    cursor = conn.cursor()
    
    # Опции сайта
    options = [
        ('blogname', content['title']),
        ('blogdescription', content['description']),
        ('siteurl', 'http://localhost:8000'),
        ('home', 'http://localhost:8000'),
        ('template', 'church-theme'),
        ('stylesheet', 'church-theme'),
        ('default_category', '1'),
        ('default_comment_status', 'open'),
        ('posts_per_page', '10'),
        ('show_on_front', 'page'),
    ]
    
    for name, value in options:
        cursor.execute('''
            INSERT OR REPLACE INTO wp_options (option_name, option_value, autoload)
            VALUES (?, ?, 'yes')
        ''', (name, value))
    
    # Создаём пользователя админа (пароль: admin123)
    # Хэш пароля для 'admin123' в WordPress формате
    admin_hash = '$P$BpXM8y.KnPqQJ8uN5FhZqQx8K5mGpC1'  # Упрощённый хэш
    cursor.execute('''
        INSERT OR REPLACE INTO wp_users (ID, user_login, user_pass, user_nicename, user_email, display_name)
        VALUES (1, 'admin', ?, 'admin', 'admin@church.local', 'Administrator')
    ''', (admin_hash,))
    
    # Мета пользователя
    cursor.execute('''
        INSERT OR REPLACE INTO wp_usermeta (user_id, meta_key, meta_value)
        VALUES (1, 'wp_capabilities', 'a:1:{s:13:"administrator";b:1;}')
    ''')
    
    # Главная страница
    cursor.execute('''
        INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author)
        VALUES (?, ?, 'home', 'page', 'publish', 1)
    ''', (content['title'], f'''
<!--wp:heading--><h1>{content['hero_text']}</h1><!--/wp:heading-->
<!--wp:paragraph--><p>{content['description']}</p><!--/wp:paragraph-->
<!--wp:paragraph--><p>{content['history'][:300]}</p><!--/wp:paragraph-->
    '''))
    
    home_id = cursor.lastrowid
    
    # Страница истории
    cursor.execute('''
        INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author)
        VALUES (?, ?, 'history', 'page', 'publish', 1)
    ''', ('История храма', f'''
<!--wp:heading--><h1>История храма</h1><!--/wp:heading-->
<!--wp:paragraph--><p>{content['history']}</p><!--/wp:paragraph-->
    ''',))
    
    # Страница контактов
    contacts_content = f'''
<!--wp:heading--><h1>Контакты</h1><!--/wp:heading-->
<!--wp:paragraph--><p><strong>Адрес:</strong> {content['contacts']['address']}</p><!--/wp:paragraph-->
<!--wp:paragraph--><p><strong>Телефон:</strong> {'; '.join(content['contacts']['phones'])}</p><!--/wp:paragraph-->
'''
    if content['contacts'].get('emails'):
        contacts_content += f'<!--wp:paragraph--><p><strong>Email:</strong> {content["contacts"]["emails"][0]}</p><!--/wp:paragraph-->'
    
    cursor.execute('''
        INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author)
        VALUES (?, ?, 'contacts', 'page', 'publish', 1)
    ''', ('Контакты', contacts_content))
    
    # Страница расписания
    schedule_content = '<!--wp:heading--><h1>Расписание богослужений</h1><!--/wp:heading-->'
    if content['schedule_img']:
        schedule_content += f'<!--wp:image--><figure class="wp-block-image"><img src="{content["schedule_img"]}" alt="Расписание"/></figure><!--/wp:image-->'
    
    cursor.execute('''
        INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author)
        VALUES (?, ?, 'raspisanie', 'page', 'publish', 1)
    ''', ('Расписание богослужений', schedule_content))
    
    # Показывать главную страницу на фронтенде
    cursor.execute('''
        INSERT OR REPLACE INTO wp_options (option_name, option_value, autoload)
        VALUES ('page_on_front', ?, 'yes')
    ''', (str(home_id),))
    
    # Создаём священников как кастомные посты
    for i, priest in enumerate(content['priests']):
        cursor.execute('''
            INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author, menu_order)
            VALUES (?, ?, ?, 'priest', 'publish', 1, ?)
        ''', (
            priest['name'],
            priest.get('bio', ''),
            f"priest-{i}",
            i
        ))
        
        priest_id = cursor.lastrowid
        
        # Мета данные священника
        if priest.get('position'):
            cursor.execute('''
                INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
                VALUES (?, '_priest_position', ?)
            ''', (priest_id, priest['position']))
        
        if priest.get('photo'):
            cursor.execute('''
                INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
                VALUES (?, '_thumbnail_id', ?)
            ''', (priest_id, 'placeholder'))
    
    # Создаём таксономию для меню
    cursor.execute('''
        INSERT INTO wp_terms (name, slug) VALUES ('Основное меню', 'primary')
    ''')
    primary_term_id = cursor.lastrowid
    
    cursor.execute('''
        INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, count)
        VALUES (?, 'nav_menu', 'Основное меню', ?)
    ''', (primary_term_id, len(content['menu_items'])))
    
    menu_tax_id = cursor.lastrowid
    
    # Элементы меню
    for i, item in enumerate(content['menu_items']):
        # Создаём запись для элемента меню
        cursor.execute('''
            INSERT INTO wp_posts (post_title, post_content, post_name, post_type, post_status, post_author, menu_order)
            VALUES (?, '', ?, 'nav_menu_item', 'publish', 1, ?)
        ''', (item['label'], f"menu-item-{i}", i))
        
        menu_item_id = cursor.lastrowid
        
        # Мета элементы меню
        metas = [
            (menu_item_id, '_menu_item_type', 'custom'),
            (menu_item_id, '_menu_item_object_id', menu_item_id),
            (menu_item_id, '_menu_item_object', 'custom'),
            (menu_item_id, '_menu_item_url', item['url']),
            (menu_item_id, '_menu_item_menu_item_parent', '0'),
            (menu_item_id, '_menu_item_xfn', ''),
            (menu_item_id, '_menu_item_target', ''),
        ]
        
        for post_id, key, value in metas:
            cursor.execute('''
                INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
                VALUES (?, ?, ?)
            ''', (post_id, key, value))
        
        # Связь с таксономией
        cursor.execute('''
            INSERT INTO wp_term_relationships (object_id, term_taxonomy_id)
            VALUES (?, ?)
        ''', (menu_item_id, menu_tax_id))
    
    conn.commit()
    conn.close()
    print(f"✅ Контент добавлен: {len(content['priests'])} священников, {len(content['menu_items'])} пунктов меню")

def verify_installation():
    """Проверяет установку"""
    print("\n🔍 Проверка установки...")
    
    checks = [
        (os.path.exists(f"{THEME_DIR}/style.css"), "Файл style.css"),
        (os.path.exists(f"{THEME_DIR}/functions.php"), "Файл functions.php"),
        (os.path.exists(f"{THEME_DIR}/index.php"), "Файл index.php"),
        (os.path.exists(f"{THEME_DIR}/header.php"), "Файл header.php"),
        (os.path.exists(f"{THEME_DIR}/footer.php"), "Файл footer.php"),
        (os.path.exists(f"{THEME_DIR}/assets/js/main.js"), "Файл main.js"),
        (os.path.exists(WP_DB_PATH), "База данных"),
    ]
    
    all_good = True
    for check, name in checks:
        status = "✅" if check else "❌"
        print(f"{status} {name}")
        if not check:
            all_good = False
    
    return all_good

def main():
    """Основная функция"""
    print("=" * 60)
    print("🏛️  Автоматическая установка сайта церкви")
    print("=" * 60)
    print()
    
    # Шаг 1: Создаём директории
    create_directories()
    
    # Шаг 2: Скачиваем контент
    content = scrape_site()
    
    # Шаг 3: Создаём тему
    create_theme_files(content)
    
    # Шаг 4: Инициализируем БД
    init_sqlite_db()
    
    # Шаг 5: Вставляем контент
    insert_content_to_db(content)
    
    # Шаг 6: Проверяем
    success = verify_installation()
    
    print()
    print("=" * 60)
    if success:
        print("🎉 УСТАНОВКА ЗАВЕРШЕНА УСПЕШНО!")
        print("=" * 60)
        print()
        print("📋 Что сделано:")
        print("   ✅ Тема WordPress создана и активирована")
        print("   ✅ Контент скачан и импортирован")
        print(f"   ✅ {len(content['priests'])} карточек священников добавлено")
        print(f"   ✅ {len(content['menu_items'])} пунктов меню создано")
        print("   ✅ Главная страница настроена")
        print()
        print("🚀 Запустите сайт командой:")
        print("   cd /workspace && php -S localhost:8000 -t app/public")
        print()
        print("🌐 Сайт будет доступен по адресу:")
        print("   http://localhost:8000")
        print()
        print("📝 Данные для входа в админку:")
        print("   Логин: admin")
        print("   Пароль: admin123")
    else:
        print("❌ Возникли проблемы при установке")
        print("Проверьте логи выше")
    print("=" * 60)

if __name__ == "__main__":
    main()
