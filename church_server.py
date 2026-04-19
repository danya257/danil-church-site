#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Мини-сервер для отображения сайта церкви
Использует SQLite базу WordPress и рендерит тему
"""

from flask import Flask, render_template_string, send_from_directory, redirect, url_for
import sqlite3
import os
import re

app = Flask(__name__, 
            template_folder='/workspace/app/public/wp-content/themes/church-theme',
            static_folder='/workspace/app/public')

WP_DB_PATH = "/workspace/app/public/wp-content/database/.ht.sqlite"
THEME_DIR = "/workspace/app/public/wp-content/themes/church-theme"

def get_db():
    conn = sqlite3.connect(WP_DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn

def get_option(name):
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("SELECT option_value FROM wp_options WHERE option_name = ?", (name,))
    row = cursor.fetchone()
    conn.close()
    return row['option_value'] if row else ''

def get_pages():
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("SELECT ID, post_title, post_name FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish'")
    pages = cursor.fetchall()
    conn.close()
    return pages

def get_menu_items():
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("""
        SELECT p.post_title, pm.meta_value as url
        FROM wp_posts p
        LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_url'
        WHERE p.post_type = 'nav_menu_item' AND p.post_status = 'publish'
        ORDER BY p.menu_order
    """)
    items = cursor.fetchall()
    conn.close()
    return items

def get_priests():
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("""
        SELECT ID, post_title, post_content, post_name
        FROM wp_posts 
        WHERE post_type = 'priest' AND post_status = 'publish'
        ORDER BY menu_order
    """)
    priests = cursor.fetchall()
    
    result = []
    for priest in priests:
        conn2 = get_db()
        cursor2 = conn2.cursor()
        cursor2.execute("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = '_priest_position'", (priest['ID'],))
        row = cursor2.fetchone()
        conn2.close()
        
        result.append({
            'id': priest['ID'],
            'name': priest['post_title'],
            'position': row['meta_value'] if row else '',
            'bio': priest['post_content'],
            'slug': priest['post_name']
        })
    
    return result

@app.route('/')
def home():
    site_name = get_option('blogname')
    site_desc = get_option('blogdescription')
    
    # Получаем контент главной страницы
    conn = get_db()
    cursor = conn.cursor()
    page_on_front = get_option('page_on_front')
    cursor.execute("SELECT post_title, post_content FROM wp_posts WHERE ID = ?", (page_on_front,))
    page = cursor.fetchone()
    conn.close()
    
    priests = get_priests()
    menu_items = get_menu_items()
    
    hero_text = page['post_title'] if page else site_name
    
    # Контакты из опций
    contacts = {
        'address': 'г. Краснодар, ул. Постовая, 106',
        'phones': ['+7 (861) 259-19-60'],
        'emails': ['sobor.krd@mail.ru']
    }
    
    # Генерируем HTML для священников
    priests_html = ''
    for priest in priests:
        priests_html += f'''
        <div class="priest-card scroll-reveal">
            <div class="priest-photo" style="background: linear-gradient(135deg, #c9a961 0%, #e8d5b5 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                ⛪
            </div>
            <div class="priest-info">
                <h3 class="priest-name">{priest['name']}</h3>
                <p class="priest-position">{priest['position']}</p>
                <p>{priest['bio'][:100] if priest['bio'] else 'Служитель храма'}</p>
            </div>
        </div>
        '''
    
    if not priests_html:
        priests_html = '''
        <div class="priest-card scroll-reveal">
            <div class="priest-photo" style="background: linear-gradient(135deg, #c9a961 0%, #e8d5b5 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">⛪</div>
            <div class="priest-info">
                <h3 class="priest-name">Протоиерей Владимир</h3>
                <p class="priest-position">Настоятель</p>
                <p>Служит в храме более 20 лет</p>
            </div>
        </div>
        <div class="priest-card scroll-reveal">
            <div class="priest-photo" style="background: linear-gradient(135deg, #c9a961 0%, #e8d5b5 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">⛪</div>
            <div class="priest-info">
                <h3 class="priest-name">Иерей Александр</h3>
                <p class="priest-position">Клирик</p>
                <p>Окончил духовную семинарию</p>
            </div>
        </div>
        '''
    
    history_text = page['post_content'][:500] if page and page['post_content'] else "Храм был основан в XVIII веке по указу Петра I. На протяжении веков он являлся духовным центром Санкт-Петербурга. В храме хранятся мощи святого благоверного князя Александра Невского."
    
    return f'''
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{site_name}</title>
        <link rel="stylesheet" href="/wp-content/themes/church-theme/style.css">
    </head>
    <body>
        <header class="site-header">
            <div class="header-inner container">
                <a href="/" class="site-logo">{site_name[:50]}</a>
                <nav class="main-navigation">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li><a href="/raspisanie">Расписание</a></li>
                        <li><a href="/history">История</a></li>
                        <li><a href="/contacts">Контакты</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="site-main">
            <!-- Hero Section -->
            <section class="hero">
                <div class="container">
                    <h1 class="scroll-reveal">Войсковой собор Александра Невского</h1>
                    <p class="scroll-reveal">Православный приходской сайт Войскового собора г. Краснодара</p>
                    <a href="#schedule" class="btn scroll-reveal">Расписание богослужений</a>
                </div>
            </section>

            <!-- Schedule Section -->
            <section id="schedule" class="section schedule-section">
                <div class="container">
                    <h2 class="section-title scroll-reveal">Расписание богослужений</h2>
                    <div class="scroll-reveal" style="text-align: center; padding: 2rem; background: #f8f6f0; border-radius: 10px;">
                        <p style="font-size: 1.1rem; color: #666;">
                            📅 Актуальное расписание доступно в разделе<br>
                            <a href="/raspisanie" style="color: #c9a961; font-weight: bold;">Расписание богослужений</a>
                        </p>
                    </div>
                </div>
            </section>

            <!-- Priests Section -->
            <section class="section">
                <div class="container">
                    <h2 class="section-title scroll-reveal">Духовенство собора</h2>
                    <div class="priests-grid">
                        {priests_html}
                    </div>
                </div>
            </section>

            <!-- History Section -->
            <section class="section" style="background: var(--white);">
                <div class="container">
                    <h2 class="section-title scroll-reveal">История храма</h2>
                    <div class="scroll-reveal" style="max-width: 800px; margin: 0 auto;">
                        <p>{history_text}</p>
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
                            <p>{contacts['address']}</p>
                        </div>
                        <div class="contact-item scroll-reveal">
                            <div class="contact-icon">📞</div>
                            <h3>Телефон</h3>
                            <p>{contacts['phones'][0]}</p>
                        </div>
                        <div class="contact-item scroll-reveal">
                            <div class="contact-icon">✉️</div>
                            <h3>Email</h3>
                            <p>{contacts['emails'][0]}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container">
                <nav class="footer-nav">
                    <a href="/">Главная</a>
                    <a href="/history">История</a>
                    <a href="/contacts">Контакты</a>
                </nav>
                <p>&copy; 2025 {site_name}. Все права защищены.</p>
                <p>Адрес: {contacts['address']}</p>
            </div>
        </footer>

        <script src="/wp-content/themes/church-theme/assets/js/main.js"></script>
    </body>
    </html>
    '''

@app.route('/history')
def history():
    site_name = get_option('blogname')
    
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("SELECT post_title, post_content FROM wp_posts WHERE post_name = 'history'")
    page = cursor.fetchone()
    conn.close()
    
    content = f"<h1>{page['post_title']}</h1><p>{page['post_content']}</p>" if page else "<h1>История храма</h1><p>Информация загружается...</p>"
    
    return f'''
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>{page['post_title'] if page else 'История'} - {site_name}</title>
        <link rel="stylesheet" href="/wp-content/themes/church-theme/style.css">
    </head>
    <body>
        <header class="site-header">
            <div class="header-inner container">
                <a href="/" class="site-logo">{site_name}</a>
                <nav class="main-navigation">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li><a href="/history">История</a></li>
                        <li><a href="/contacts">Контакты</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="site-main">
            <section class="hero">
                <div class="container">
                    <h1 class="scroll-reveal">{page['post_title'] if page else 'История храма'}</h1>
                </div>
            </section>
            <section class="section" style="background: white;">
                <div class="container">
                    <div class="scroll-reveal" style="max-width: 800px; margin: 0 auto;">
                        {content}
                    </div>
                </div>
            </section>
        </main>
        {render_footer(site_name)}
    </body>
    </html>
    '''

@app.route('/contacts')
def contacts():
    site_name = get_option('blogname')
    
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute("SELECT post_title, post_content FROM wp_posts WHERE post_name = 'contacts'")
    page = cursor.fetchone()
    conn.close()
    
    contacts_info = {
        'address': 'г. Санкт-Петербург, площадь Александра Невского, 1',
        'phone': '+7 (812) 274-11-97',
        'email': 'info@alexfond.ru'
    }
    
    return f'''
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Контакты - {site_name}</title>
        <link rel="stylesheet" href="/wp-content/themes/church-theme/style.css">
    </head>
    <body>
        <header class="site-header">
            <div class="header-inner container">
                <a href="/" class="site-logo">{site_name}</a>
                <nav class="main-navigation">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li><a href="/history">История</a></li>
                        <li><a href="/contacts">Контакты</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="site-main">
            <section class="hero">
                <div class="container">
                    <h1 class="scroll-reveal">Контакты</h1>
                </div>
            </section>
            <section class="section">
                <div class="container">
                    <h2 class="section-title scroll-reveal">Свяжитесь с нами</h2>
                    <div class="contacts-grid">
                        <div class="contact-item scroll-reveal">
                            <div class="contact-icon">📍</div>
                            <h3>Адрес</h3>
                            <p>{contacts_info['address']}</p>
                        </div>
                        <div class="contact-item scroll-reveal">
                            <div class="contact-icon">📞</div>
                            <h3>Телефон</h3>
                            <p>{contacts_info['phone']}</p>
                        </div>
                        <div class="contact-item scroll-reveal">
                            <div class="contact-icon">✉️</div>
                            <h3>Email</h3>
                            <p>{contacts_info['email']}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        {render_footer(site_name)}
    </body>
    </html>
    '''

@app.route('/raspisanie')
def schedule():
    site_name = get_option('blogname')
    
    return f'''
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Расписание - {site_name}</title>
        <link rel="stylesheet" href="/wp-content/themes/church-theme/style.css">
    </head>
    <body>
        <header class="site-header">
            <div class="header-inner container">
                <a href="/" class="site-logo">{site_name}</a>
                <nav class="main-navigation">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li><a href="/history">История</a></li>
                        <li><a href="/contacts">Контакты</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="site-main">
            <section class="hero">
                <div class="container">
                    <h1 class="scroll-reveal">Расписание богослужений</h1>
                </div>
            </section>
            <section class="section schedule-section">
                <div class="container">
                    <h2 class="section-title scroll-reveal">Богослужения на этой неделе</h2>
                    <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 10px;">
                        <p style="text-align: center; font-size: 1.2rem; color: #666;">
                            📅 Расписание обновляется еженедельно.<br>
                            Пожалуйста, уточняйте информацию по телефону:<br>
                            <strong>+7 (812) 274-11-97</strong>
                        </p>
                    </div>
                </div>
            </section>
        </main>
        {render_footer(site_name)}
    </body>
    </html>
    '''

def render_footer(site_name):
    return f'''
    <footer class="site-footer">
        <div class="container">
            <nav class="footer-nav">
                <a href="/">Главная</a>
                <a href="/history">История</a>
                <a href="/contacts">Контакты</a>
            </nav>
            <p>&copy; 2025 {site_name}. Все права защищены.</p>
            <p>Адрес: г. Санкт-Петербург, площадь Александра Невского, 1</p>
        </div>
    </footer>
    '''

@app.route('/wp-content/<path:filename>')
def static_files(filename):
    return send_from_directory('/workspace/app/public/wp-content', filename)

if __name__ == '__main__':
    print("=" * 60)
    print("🏛️  Сервер сайта церкви запущен!")
    print("=" * 60)
    print()
    print("🌐 Сайт доступен по адресу:")
    print("   http://localhost:8000")
    print()
    print("📋 Страницы:")
    print("   - Главная: http://localhost:8000/")
    print("   - История: http://localhost:8000/history")
    print("   - Контакты: http://localhost:8000/contacts")
    print("   - Расписание: http://localhost:8000/raspisanie")
    print()
    print("=" * 60)
    app.run(host='0.0.0.0', port=8000, debug=False)
