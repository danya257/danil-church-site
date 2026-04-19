# -*- coding: utf-8 -*-
"""
🏛️ ФИНАЛЬНАЯ ВЕРСИЯ САЙТА ЦЕРКВИ
Абсолютно автономный скрипт. Не требует внешних файлов шаблонов.
Все данные хранятся в SQLite базе данных.
"""

import os
import sqlite3
import threading
import time
from flask import Flask, render_template_string, g, request, redirect, url_for

# ================= КОНФИГУРАЦИЯ =================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DB_PATH = os.path.join(BASE_DIR, 'church_data.db')
PORT = 8000

app = Flask(__name__)

# ================= БАЗА ДАННЫХ =================
def get_db():
    if not hasattr(g, 'sqlite_db'):
        g.sqlite_db = sqlite3.connect(DB_PATH)
        g.sqlite_db.row_factory = sqlite3.Row
    return g.sqlite_db

def init_db():
    """Инициализирует базу данных и заполняет контентом"""
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    # Таблица настроек
    c.execute('''CREATE TABLE IF NOT EXISTS options (
        key TEXT PRIMARY KEY,
        value TEXT
    )''')
    
    # Таблица страниц
    c.execute('''CREATE TABLE IF NOT EXISTS pages (
        slug TEXT PRIMARY KEY,
        title TEXT,
        content TEXT,
        order_num INTEGER
    )''')
    
    # Таблица священников
    c.execute('''CREATE TABLE IF NOT EXISTS clergy (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        position TEXT,
        description TEXT,
        image_url TEXT
    )''')
    
    # Таблица расписания
    c.execute('''CREATE TABLE IF NOT EXISTS schedule (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        day TEXT,
        time TEXT,
        service TEXT,
        note TEXT
    )''')
    
    # Таблица новостей
    c.execute('''CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        date TEXT,
        excerpt TEXT,
        content TEXT
    )''')

    # Проверка, пуста ли база (чтобы не заполнять повторно)
    c.execute("SELECT count(*) FROM options")
    if c.fetchone()[0] == 0:
        print("📦 Заполнение базы данных контентом...")
        
        # Настройки
        settings = [
            ('site_name', 'Собор Александра Невского'),
            ('site_description', 'Православный собор в городе Данилов'),
            ('address', 'г. Данилов, ул. Советская, д. 1'),
            ('phone', '+7 (48538) 2-12-34'),
            ('email', 'sobor@danilov.ru'),
            ('vk_link', 'https://vk.com/alexander_nevsky_sobor'),
            ('telegram_link', 'https://t.me/alexander_nevsky'),
        ]
        c.executemany("INSERT OR REPLACE INTO options (key, value) VALUES (?, ?)", settings)
        
        # Страницы
        pages = [
            ('home', 'Главная', '', 0),
            ('history', 'История храма', '''
                <div class="content-block">
                    <h1>История Собора Александра Невского</h1>
                    <p class="lead">Храм был основан в конце XIX века и является духовным центром нашего города.</p>
                    
                    <h2>Основание</h2>
                    <p>Строительство собора началось в 1895 году по указу императора Александра III. Проект был разработан известным архитектором Константином Тоном в русско-византийском стиле.</p>
                    
                    <h2>Советский период</h2>
                    <p>В 1930-е годы храм был закрыт, колокольня разрушена, а в здании располагался склад. Несмотря на трудности, верующие тайно собирались для молитвы.</p>
                    
                    <h2>Возрождение</h2>
                    <p>В 1990 году храм был возвращен Церкви. Начались масштабные реставрационные работы. Была восстановлена колокольня, обновлены купола и внутреннее убранство.</p>
                    
                    <blockquote>
                        "На камне сем созижду Церковь Мою, и врата адская не одолеют ей."
                        <cite>— Мф. 16:18</cite>
                    </blockquote>
                    
                    <p>Сегодня собор вновь открыт для всех желающих, здесь ежедневно совершаются богослужения, работает воскресная школа и социальная служба.</p>
                </div>
            ''', 2),
            ('contacts', 'Контакты', '''
                <div class="content-block">
                    <h1>Контакты</h1>
                    <div class="contact-grid">
                        <div class="contact-item">
                            <h3>📍 Адрес</h3>
                            <p>г. Данилов, ул. Советская, д. 1</p>
                        </div>
                        <div class="contact-item">
                            <h3>📞 Телефон</h3>
                            <p>+7 (48538) 2-12-34</p>
                        </div>
                        <div class="contact-item">
                            <h3>✉️ Email</h3>
                            <p>sobor@danilov.ru</p>
                        </div>
                        <div class="contact-item">
                            <h3>🕒 Время работы канцелярии</h3>
                            <p>Пн-Пт: 9:00 - 17:00<br>Сб-Вс: 8:00 - 14:00</p>
                        </div>
                    </div>
                    
                    <h2>Как добраться</h2>
                    <p>Собор находится в центре города, рядом с городской площадью. От автовокзала можно добраться на автобусе №1 до остановки "Центральная площадь".</p>
                    
                    <div class="map-placeholder">
                        <p>🗺️ Здесь будет интерактивная карта Яндекс или Google</p>
                    </div>
                </div>
            ''', 3),
            ('raspisanie', 'Расписание богослужений', '''
                <div class="content-block">
                    <h1>Расписание богослужений</h1>
                    <p class="lead">Актуальное расписание служб на текущую неделю.</p>
                    <div class="schedule-table-wrapper">
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>День недели</th>
                                    <th>Время</th>
                                    <th>Богослужение</th>
                                    <th>Примечание</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Заполняется из БД -->
                            </tbody>
                        </table>
                    </div>
                    <p class="note">* Расписание может меняться. Уточняйте информацию в церковной лавке или по телефону.</p>
                </div>
            ''', 1),
        ]
        c.executemany("INSERT OR REPLACE INTO pages (slug, title, content, order_num) VALUES (?, ?, ?, ?)", pages)
        
        # Священники
        clergy = [
            ('Протоиерей Александр Иванов', 'Настоятель собора', 'Окончил Московскую Духовную Академию. Служит в соборе более 20 лет. Возглавляет епархиальный отдел по работе с молодежью.', '/static/priest1.jpg'),
            ('Иерей Петр Петров', 'Ключарь', 'Ответственный за организацию богослужений. Проповедник, автор нескольких книг по богословию.', '/static/priest2.jpg'),
            ('Иерей Сергий Сидоров', 'Священник', 'Руководитель воскресной школы. Занимается социальной работой и помощью нуждающимся.', '/static/priest3.jpg'),
            ('Диакон Андрей Андреев', 'Диакон', 'Помощник настоятеля в совершении таинств. Ответственный за работу с хором.', '/static/deacon1.jpg'),
        ]
        c.executemany("INSERT INTO clergy (name, position, description, image_url) VALUES (?, ?, ?, ?)", clergy)
        
        # Расписание (пример на неделю) - ИСПРАВЛЕНО: 4 поля вместо 5
        schedule = [
            ('2024-04-20', '17:00', 'Всенощное бдение', ''),
            ('2024-04-21', '09:00', 'Божественная литургия', 'Неделя 3-я по Пасхе'),
            ('2024-04-21', '17:00', 'Вечернее богослужение', ''),
            ('2024-04-22', '08:00', 'Божественная литургия', ''),
            ('2024-04-24', '17:00', 'Молебен с акафистом', 'Акафист Александру Невскому'),
            ('2024-04-26', '17:00', 'Всенощное бдение', ''),
            ('2024-04-27', '09:00', 'Божественная литургия', 'Поминовение усопших'),
        ]
        c.executemany("INSERT INTO schedule (day, time, service, note) VALUES (?, ?, ?, ?)", schedule)
        
        # Новости
        news = [
            ('Праздник Пасхи в нашем соборе', '2024-04-15', 'Приглашаем всех верующих встретить Светлое Христово Воскресение...', 'Полный текст новости о праздновании Пасхи...'),
            ('Начало работы воскресной школы', '2024-04-10', 'Объявляется набор детей в группы воскресной школы...', 'Подробности о расписании занятий и предметах...'),
            ('Паломническая поездка', '2024-04-05', 'Планируется поездка в Троице-Сергиеву Лавру...', 'Детали поездки, стоимость и программа...'),
        ]
        c.executemany("INSERT INTO news (title, date, excerpt, content) VALUES (?, ?, ?, ?)", news)
        
        conn.commit()
        print("✅ База данных успешно заполнена!")
    else:
        print("ℹ️ База данных уже существует.")
    
    conn.close()

@app.teardown_appcontext
def close_db(error):
    if hasattr(g, 'sqlite_db'):
        g.sqlite_db.close()

# ================= ШАБЛОНЫ (HTML ВНУТРИ КОДА) =================

BASE_TEMPLATE = """
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ site_name }}{% if title %} | {{ title }}{% endif %}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #c9a961;
            --accent: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --font-main: 'Inter', sans-serif;
            --font-heading: 'Playfair Display', serif;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: var(--font-main);
            line-height: 1.6;
            color: var(--dark);
            background-color: #fff;
        }
        
        /* Header */
        header {
            background: var(--primary);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
        }
        
        nav a:hover, nav a.active {
            color: var(--secondary);
            border-bottom-color: var(--secondary);
        }
        
        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(26, 26, 46, 0.7), rgba(26, 26, 46, 0.7)), 
                        url('https://images.unsplash.com/photo-1548625361-ec8f3b08e693?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-family: var(--font-heading);
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }
        
        .btn {
            display: inline-block;
            background: var(--secondary);
            color: var(--primary);
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #e0b86e;
            transform: translateY(-2px);
        }
        
        /* Sections */
        section {
            padding: 80px 0;
        }
        
        .section-title {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--primary);
            position: relative;
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: var(--secondary);
            margin: 1rem auto 0;
        }
        
        /* Clergy Grid */
        .clergy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .clergy-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .clergy-card:hover {
            transform: translateY(-5px);
        }
        
        .clergy-img {
            height: 250px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 3rem;
        }
        
        .clergy-info {
            padding: 1.5rem;
        }
        
        .clergy-name {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .clergy-position {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: block;
        }
        
        /* Schedule Table */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .schedule-table th,
        .schedule-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .schedule-table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }
        
        .schedule-table tr:hover {
            background: #f8f9fa;
        }
        
        /* News Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .news-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .news-date {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .news-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        /* Footer */
        footer {
            background: var(--primary);
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .footer-section h3 {
            font-family: var(--font-heading);
            color: var(--secondary);
            margin-bottom: 1rem;
        }
        
        .footer-section a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .footer-section a:hover {
            color: var(--secondary);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
        }
        
        /* Content Pages */
        .content-block {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .content-block h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .content-block h2 {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            color: var(--primary);
            margin: 2rem 0 1rem;
        }
        
        .content-block p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .content-block blockquote {
            border-left: 4px solid var(--secondary);
            padding: 1rem 2rem;
            margin: 2rem 0;
            background: #f8f9fa;
            font-style: italic;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .contact-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .contact-item h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .map-placeholder {
            background: #eee;
            height: 400px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2rem;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            nav ul { display: none; }
            .mobile-menu-btn { display: block; }
            .section-title { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="/" class="logo">
                ⛪ {{ site_name }}
            </a>
            <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
            <nav>
                <ul id="main-menu">
                    <li><a href="/" {% if active_page == 'home' %}class="active"{% endif %}>Главная</a></li>
                    <li><a href="/raspisanie" {% if active_page == 'raspisanie' %}class="active"{% endif %}>Расписание</a></li>
                    <li><a href="/history" {% if active_page == 'history' %}class="active"{% endif %}>История</a></li>
                    <li><a href="/contacts" {% if active_page == 'contacts' %}class="active"{% endif %}>Контакты</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        {% block content %}{% endblock %}
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>{{ site_name }}</h3>
                    <p>{{ site_description }}</p>
                </div>
                <div class="footer-section">
                    <h3>Контакты</h3>
                    <p>📍 {{ address }}</p>
                    <p>📞 {{ phone }}</p>
                    <p>✉️ {{ email }}</p>
                </div>
                <div class="footer-section">
                    <h3>Соцсети</h3>
                    <a href="{{ vk_link }}" target="_blank">ВКонтакте</a>
                    <a href="{{ telegram_link }}" target="_blank">Telegram</a>
                </div>
            </div>
            <div class="copyright">
                &copy; {{ site_name }}. Все права защищены.
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('main-menu');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
                menu.style.flexDirection = 'column';
                menu.style.position = 'absolute';
                menu.style.top = '100%';
                menu.style.left = '0';
                menu.style.right = '0';
                menu.style.background = '#1a1a2e';
                menu.style.padding = '1rem';
            }
        }
    </script>
</body>
</html>
"""

HOME_TEMPLATE = """
<section class="hero">
    <div class="container">
        <h1>Добро пожаловать в наш собор</h1>
        <p>Место молитвы, духовного роста и общения с Богом</p>
        <a href="/raspisanie" class="btn">Расписание богослужений</a>
    </div>
</section>

<section>
    <div class="container">
        <h2 class="section-title">Наши священнослужители</h2>
        <div class="clergy-grid">
            {% for priest in clergy %}
            <div class="clergy-card">
                <div class="clergy-img">👤</div>
                <div class="clergy-info">
                    <h3 class="clergy-name">{{ priest.name }}</h3>
                    <span class="clergy-position">{{ priest.position }}</span>
                    <p>{{ priest.description[:100] }}...</p>
                </div>
            </div>
            {% endfor %}
        </div>
    </div>
</section>

<section style="background: #f8f9fa;">
    <div class="container">
        <h2 class="section-title">Последние новости</h2>
        <div class="news-grid">
            {% for item in news %}
            <div class="news-card">
                <div class="news-date">{{ item.date }}</div>
                <h3 class="news-title">{{ item.title }}</h3>
                <p>{{ item.excerpt }}</p>
            </div>
            {% endfor %}
        </div>
    </div>
</section>
"""

SCHEDULE_TEMPLATE = """
<div class="container" style="padding: 4rem 20px;">
    <h1 class="section-title">Расписание богослужений</h1>
    <div class="schedule-table-wrapper">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>День</th>
                    <th>Время</th>
                    <th>Служба</th>
                    <th>Примечание</th>
                </tr>
            </thead>
            <tbody>
                {% for item in schedule %}
                <tr>
                    <td>{{ item.day }}</td>
                    <td>{{ item.day_name }}</td>
                    <td>{{ item.time }}</td>
                    <td>{{ item.service }}</td>
                    <td>{{ item.note }}</td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
    <p class="note" style="margin-top: 1rem; color: #666; font-style: italic;">
        * Расписание может меняться. Уточняйте информацию в церковной лавке.
    </p>
</div>
"""

GENERIC_PAGE_TEMPLATE = """
<div class="container" style="padding: 4rem 20px;">
    {{ content|safe }}
</div>
"""

# ================= РОУТЫ (МАРШРУТЫ) =================

@app.route('/')
def home():
    db = get_db()
    
    # Получаем настройки
    settings = {row['key']: row['value'] for row in db.execute("SELECT * FROM options")}
    
    # Получаем священников
    clergy = db.execute("SELECT * FROM clergy ORDER BY id").fetchall()
    
    # Получаем новости
    news = db.execute("SELECT * FROM news ORDER BY date DESC LIMIT 3").fetchall()
    
    full_template = BASE_TEMPLATE.replace('{% block content %}{% endblock %}', HOME_TEMPLATE)
    
    return render_template_string(full_template,
                                  site_name=settings.get('site_name', 'Церковь'),
                                  site_description=settings.get('site_description', ''),
                                  address=settings.get('address', ''),
                                  phone=settings.get('phone', ''),
                                  email=settings.get('email', ''),
                                  vk_link=settings.get('vk_link', '#'),
                                  telegram_link=settings.get('telegram_link', '#'),
                                  active_page='home',
                                  clergy=clergy,
                                  news=news)

@app.route('/raspisanie')
def schedule():
    db = get_db()
    settings = {row['key']: row['value'] for row in db.execute("SELECT * FROM options")}
    
    schedule_items = db.execute("SELECT * FROM schedule ORDER BY day, time").fetchall()
    
    # Добавляем названия дней недели (упрощенно)
    days_map = {
        '2024-04-20': 'Суббота', '2024-04-21': 'Воскресенье',
        '2024-04-22': 'Понедельник', '2024-04-23': 'Вторник',
        '2024-04-24': 'Среда', '2024-04-25': 'Четверг',
        '2024-04-26': 'Пятница', '2024-04-27': 'Суббота'
    }
    
    formatted_schedule = []
    for item in schedule_items:
        item_dict = dict(item)
        # Пытаемся получить день недели из даты или используем поле day
        item_dict['day_name'] = days_map.get(item['day'], item['day'])
        formatted_schedule.append(item_dict)
    
    full_template = BASE_TEMPLATE.replace('{% block content %}{% endblock %}', SCHEDULE_TEMPLATE)
    
    return render_template_string(full_template,
                                  site_name=settings.get('site_name', 'Церковь'),
                                  site_description=settings.get('site_description', ''),
                                  address=settings.get('address', ''),
                                  phone=settings.get('phone', ''),
                                  email=settings.get('email', ''),
                                  vk_link=settings.get('vk_link', '#'),
                                  telegram_link=settings.get('telegram_link', '#'),
                                  active_page='raspisanie',
                                  schedule=formatted_schedule)

@app.route('/history')
def history():
    db = get_db()
    settings = {row['key']: row['value'] for row in db.execute("SELECT * FROM options")}
    
    page = db.execute("SELECT * FROM pages WHERE slug = 'history'").fetchone()
    
    if not page:
        return "Страница не найдена", 404
    
    full_template = BASE_TEMPLATE.replace('{% block content %}{% endblock %}', GENERIC_PAGE_TEMPLATE)
    
    return render_template_string(full_template,
                                  site_name=settings.get('site_name', 'Церковь'),
                                  site_description=settings.get('site_description', ''),
                                  address=settings.get('address', ''),
                                  phone=settings.get('phone', ''),
                                  email=settings.get('email', ''),
                                  vk_link=settings.get('vk_link', '#'),
                                  telegram_link=settings.get('telegram_link', '#'),
                                  active_page='history',
                                  title=page['title'],
                                  content=page['content'])

@app.route('/contacts')
def contacts():
    db = get_db()
    settings = {row['key']: row['value'] for row in db.execute("SELECT * FROM options")}
    
    page = db.execute("SELECT * FROM pages WHERE slug = 'contacts'").fetchone()
    
    if not page:
        return "Страница не найдена", 404
    
    full_template = BASE_TEMPLATE.replace('{% block content %}{% endblock %}', GENERIC_PAGE_TEMPLATE)
    
    return render_template_string(full_template,
                                  site_name=settings.get('site_name', 'Церковь'),
                                  site_description=settings.get('site_description', ''),
                                  address=settings.get('address', ''),
                                  phone=settings.get('phone', ''),
                                  email=settings.get('email', ''),
                                  vk_link=settings.get('vk_link', '#'),
                                  telegram_link=settings.get('telegram_link', '#'),
                                  active_page='contacts',
                                  title=page['title'],
                                  content=page['content'])

# ================= ЗАПУСК =================

if __name__ == '__main__':
    print("=" * 60)
    print("🏛️  ЗАПУСК САЙТА ЦЕРКВИ (Абсолютно надежная версия)")
    print("=" * 60)
    
    # Инициализация БД
    if not os.path.exists(DB_PATH):
        init_db()
    else:
        # Проверяем, есть ли данные
        conn = sqlite3.connect(DB_PATH)
        c = conn.cursor()
        c.execute("SELECT count(*) FROM options")
        if c.fetchone()[0] == 0:
            init_db()
        else:
            print("ℹ️  База данных обнаружена и готова к работе.")
        conn.close()
    
    print(f"📂 База данных: {DB_PATH}")
    print(f"🌐 Сайт доступен: http://localhost:{PORT}")
    print("=" * 60)
    
    # Запуск сервера
    app.run(host='0.0.0.0', port=PORT, debug=False)
