#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Скрипт для парсинга сайта alexander-nevskiysobor.ru и создания SQL-файла для WordPress
Автор: Senior WordPress Developer

Этот скрипт автоматически извлекает весь контент с оригинального сайта
и создает SQL файл для импорта в новую WordPress установку.
"""

import requests
from bs4 import BeautifulSoup
import re
import json
from datetime import datetime
import html as html_module

BASE_URL = "https://alexander-nevskiysobor.ru"
OUTPUT_SQL = "/workspace/app/sql/import_full_content.sql"

# Сессия для сохранения cookies
session = requests.Session()
session.headers.update({
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language': 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
})

def fetch_page(url):
    """Получение HTML страницы"""
    try:
        print(f"Загрузка: {url}")
        response = session.get(url, timeout=30)
        response.raise_for_status()
        return response.text
    except Exception as e:
        print(f"Ошибка загрузки {url}: {e}")
        return None

def parse_main_page(html_content):
    """Парсинг главной страницы"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    data = {
        'title': '',
        'description': '',
        'news': [],
        'announcements': [],
    }
    
    # Заголовок
    title_tag = soup.find('title')
    if title_tag:
        data['title'] = title_tag.get_text(strip=True)
    
    # Описание
    meta_desc = soup.find('meta', {'name': 'description'})
    if meta_desc:
        data['description'] = meta_desc.get('content', '')
    
    # Новости - ищем посты в основной ленте
    for article in soup.find_all('article', class_=re.compile(r'post|entry|item', re.I), limit=15):
        news_item = {}
        
        # Заголовок новости
        title_el = article.find(['h1', 'h2', 'h3'], class_=re.compile(r'title|heading', re.I))
        if not title_el:
            title_el = article.find(['h1', 'h2', 'h3'])
        if title_el:
            news_item['title'] = title_el.get_text(strip=True)
        
        # Дата
        date_el = article.find('time') or article.find(class_=re.compile(r'date|time|published|meta', re.I))
        if date_el:
            news_item['date'] = date_el.get_text(strip=True)
        
        # Содержание
        content_el = article.find('div', class_=re.compile(r'content|entry|text|excerpt', re.I))
        if content_el:
            news_item['content'] = str(content_el)
        else:
            # Берем все параграфы
            paragraphs = article.find_all('p')
            if paragraphs:
                news_item['content'] = ''.join(str(p) for p in paragraphs[:3])
        
        # Ссылка
        link_el = article.find('a', href=True)
        if link_el:
            href = link_el['href']
            if href.startswith('/'):
                href = BASE_URL + href
            news_item['link'] = href
        
        if news_item.get('title'):
            data['news'].append(news_item)
    
    return data

def parse_schedule_page(html_content):
    """Парсинг страницы расписания"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    schedule_data = {
        'title': 'Расписание богослужений',
        'content': ''
    }
    
    # Ищем таблицу или список с расписанием
    content_div = soup.find('div', class_='entry-content') or soup.find('main')
    
    if content_div:
        # Извлекаем таблицы
        tables = content_div.find_all('table')
        if tables:
            for table in tables:
                schedule_data['content'] += str(table) + "\n"
        
        # Если нет таблиц, берем заголовки и списки
        if not schedule_data['content']:
            for elem in content_div.find_all(['h2', 'h3', 'h4', 'p', 'ul', 'ol']):
                schedule_data['content'] += str(elem) + "\n"
    
    # Если не нашли, пробуем найти по ключевым словам
    if not schedule_data['content']:
        for elem in soup.find_all(['h2', 'h3', 'p']):
            text = elem.get_text().lower()
            if any(word in text for word in ['литургия', 'вечерня', 'утреня', 'служба', 'расписание']):
                schedule_data['content'] += str(elem) + "\n"
    
    return schedule_data

def parse_clergy_page(html_content):
    """Парсинг страницы духовенства"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    clergy_list = []
    
    # Ищем карточки священников
    clergy_cards = soup.find_all('div', class_=re.compile(r'clergy|priest|person|card', re.I))
    
    if not clergy_cards:
        # Пробуем найти по структуре
        for article in soup.find_all('article', limit=20):
            clergy_cards.append(article)
    
    for card in clergy_cards:
        clergy_member = {}
        
        # Имя
        name_el = card.find(['h2', 'h3', 'h4'])
        if name_el:
            clergy_member['name'] = name_el.get_text(strip=True)
        
        # Должность
        position_el = card.find(class_=re.compile(r'position|rank|должн', re.I))
        if position_el:
            clergy_member['position'] = position_el.get_text(strip=True)
        
        # Описание/биография
        bio_el = card.find('p')
        if bio_el:
            clergy_member['bio'] = str(bio_el)
        
        # Фото
        img_el = card.find('img')
        if img_el:
            src = img_el.get('src') or img_el.get('data-src')
            if src:
                if src.startswith('/'):
                    src = BASE_URL + src
                clergy_member['photo'] = src
        
        if clergy_member.get('name'):
            clergy_list.append(clergy_member)
    
    return clergy_list

def parse_contacts_page(html_content):
    """Парсинг страницы контактов"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    contacts = {
        'address': '',
        'phone': [],
        'email': '',
        'social': {},
        'schedule_transport': ''
    }
    
    # Ищем контактную информацию
    content_div = soup.find('div', class_='entry-content') or soup.find('main')
    
    if content_div:
        # Адрес
        address_patterns = ['адрес', 'ул.', 'улица', 'д.', 'дом', 'индекс']
        for p in content_div.find_all('p'):
            text = p.get_text().lower()
            if any(pattern in text for pattern in address_patterns):
                contacts['address'] += str(p) + "\n"
        
        # Телефоны
        phone_pattern = re.compile(r'\+?[\d\s\-\(\)]{10,}')
        for el in content_div.find_all(['p', 'span', 'a']):
            text = el.get_text()
            phones = phone_pattern.findall(text)
            for phone in phones:
                if len(phone) > 9:
                    contacts['phone'].append(phone.strip())
        
        # Email
        email_links = content_div.find_all('a', href=re.compile(r'mailto:', re.I))
        for link in email_links:
            href = link.get('href', '')
            if 'mailto:' in href:
                contacts['email'] = href.replace('mailto:', '').strip()
        
        # Соцсети
        social_links = content_div.find_all('a', href=re.compile(r'vk\.com|telegram|t\.me|youtube', re.I))
        for link in social_links:
            href = link.get('href', '')
            text = link.get_text(strip=True)
            if 'vk' in href.lower():
                contacts['social']['vk'] = href
            elif 'telegram' in href.lower() or 't.me' in href.lower():
                contacts['social']['telegram'] = href
            elif 'youtube' in href.lower():
                contacts['social']['youtube'] = href
    
    return contacts

def parse_history_page(html_content):
    """Парсинг страницы истории храма"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    history = {
        'title': 'История храма',
        'content': ''
    }
    
    content_div = soup.find('div', class_='entry-content') or soup.find('main')
    
    if content_div:
        for elem in content_div.find_all(['h2', 'h3', 'h4', 'p', 'ul', 'ol']):
            history['content'] += str(elem) + "\n"
    
    return history

def parse_activities_page(html_content):
    """Парсинг страницы деятельности (воскресная школа, благотворительность)"""
    soup = BeautifulSoup(html_content, 'html.parser')
    
    activities = []
    
    # Ищем разделы
    sections = soup.find_all('section') or soup.find_all('div', class_=re.compile(r'activity|service|деятельност', re.I))
    
    for section in sections[:10]:
        activity = {}
        
        title_el = section.find(['h2', 'h3', 'h4'])
        if title_el:
            activity['title'] = title_el.get_text(strip=True)
        
        content_el = section.find('p') or section.find('div', class_='content')
        if content_el:
            activity['content'] = str(content_el)
        
        if activity.get('title'):
            activities.append(activity)
    
    return activities

def escape_sql_string(s):
    """Экранирование строки для SQL"""
    if not s:
        return ''
    # Экранируем обратные слеши и кавычки
    s = s.replace('\\', '\\\\')
    s = s.replace("'", "''")
    s = s.replace('"', '\\"')
    return s

def create_slug(title):
    """Создание URL-friendly slug из заголовка"""
    slug = title.lower()
    slug = re.sub(r'[^\w\s-]', '', slug)
    slug = re.sub(r'[\s_]+', '-', slug)
    slug = re.sub(r'-+', '-', slug)
    return slug.strip('-')[:100]

def generate_sql_file(data):
    """Генерация SQL файла для импорта"""
    
    sql_lines = [
        "-- SQL файл для импорта контента с alexander-nevskiysobor.ru",
        f"-- Сгенерировано: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}",
        "-- Импорт страниц",
        ""
    ]
    
    # Удаляем старые записи (опционально)
    sql_lines.append("-- Очистка старых данных (если нужно)")
    sql_lines.append("-- DELETE FROM wp_posts WHERE post_type IN ('page', 'post');")
    sql_lines.append("")
    
    page_id = 100  # Начальный ID для страниц
    
    # 1. Главная страница
    if data.get('main'):
        main_content = f"""
        <div class="hero">
            <div class="hero-content">
                <h1 class="hero-title gold-shine">Войсковой собор святого благоверного князя Александра Невского</h1>
                <p class="hero-subtitle">Православный приход в городе Краснодар</p>
                <a href="#raspisanie" class="hero-button">Расписание богослужений</a>
                <a href="#contacts" class="hero-button-outline">Контакты</a>
            </div>
        </div>
        
        <section class="cta-section scroll-reveal">
            <div class="container">
                <h2>Добро пожаловать в наш храм</h2>
                <p>{escape_sql_string(data['main'].get('description', 'Приглашаем вас посетить наш собор для молитвы и участия в таинствах.'))}</p>
            </div>
        </section>
        """
        
        if data['main'].get('news'):
            main_content += "\n<section class='news-section'><h2>Последние новости</h2><div class='news-grid'>"
            for news in data['main']['news'][:6]:
                main_content += f"""
                <article class='feature-card scroll-reveal'>
                    <h3>{escape_sql_string(news.get('title', 'Новость'))}</h3>
                    {escape_sql_string(news.get('content', '')[:500])}
                </article>
                """
            main_content += "</div></section>"
        
        sql_lines.append(f"""
-- Главная страница
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '{escape_sql_string(main_content)}', 'Главная', '', 'publish', 'closed', 'closed', '', 'index', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # 2. Страница Расписание
    if data.get('schedule'):
        sql_lines.append(f"""
-- Страница Расписание богослужений
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '<h2>Расписание богослужений</h2>{escape_sql_string(data["schedule"].get("content", ""))}', 'Расписание богослужений', '', 'publish', 'closed', 'closed', '', 'raspisanie-bogosluzhenij', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # 3. Страница Духовенство
    if data.get('clergy'):
        clergy_content = "<h2>Духовенство собора</h2><div class='clergy-grid'>"
        for member in data['clergy']:
            photo_html = ""
            if member.get('photo'):
                photo_html = f"<img src='{member['photo']}' alt='{member.get('name', '')}' class='clergy-photo'>"
            
            clergy_content += f"""
            <div class='feature-card scroll-reveal'>
                {photo_html}
                <h3>{escape_sql_string(member.get('name', ''))}</h3>
                <p class='position'>{escape_sql_string(member.get('position', ''))}</p>
                {escape_sql_string(member.get('bio', ''))}
            </div>
            """
        clergy_content += "</div>"
        
        sql_lines.append(f"""
-- Страница Духовенство
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '{escape_sql_string(clergy_content)}', 'Духовенство собора', '', 'publish', 'closed', 'closed', '', 'duhovenstvo', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # 4. Страница Контакты
    if data.get('contacts'):
        contacts_content = f"""
        <div class="contacts-section">
            <h2>Контакты</h2>
            <div class="contacts-grid">
                <div class="contacts-info">
                    <h3>Войсковой собор святого благоверного князя Александра Невского</h3>
                    
                    <h4>Адрес:</h4>
                    {escape_sql_string(data['contacts'].get('address', 'г. Краснодар, ул. Постовая, д. 26'))}
                    
                    <h4>Телефоны:</h4>
                    <p>{'<br>'.join(escape_sql_string(p) for p in data['contacts'].get('phone', []))}</p>
                    
                    <h4>E-mail:</h4>
                    <p><a href="mailto:{escape_sql_string(data['contacts'].get('email', 'nevskiy-sobor@mail.ru'))}">{escape_sql_string(data['contacts'].get('email', 'nevskiy-sobor@mail.ru'))}</a></p>
                    
                    <h4>Социальные сети:</h4>
                    <p>
                        {f'<a href="{data["contacts"]["social"].get("vk", "#")}">ВКонтакте</a>' if data['contacts'].get('social', {}).get('vk') else ''}
                        {f' <a href="{data["contacts"]["social"].get("telegram", "#")}">Telegram</a>' if data['contacts'].get('social', {}).get('telegram') else ''}
                    </p>
                    
                    <h4>Проезд:</h4>
                    <p>Трамвай: 2, 4 – до ост. Городской сад<br>
                    Троллейбус: 7, 12, 20 – до ост. ул. Советская; 9, 10 – до ост. ул. Постовая<br>
                    Маршрутное такси: 3, 19Б (49), 26А, 44</p>
                </div>
                
                <div class="contacts-map">
                    <iframe src="https://yandex.ru/map-widget/v1/?ll=38.986755%2C45.032469&z=17&pt=38.986755,45.032469" width="100%" height="450" frameborder="0" allowfullscreen="true"></iframe>
                </div>
            </div>
        </div>
        """
        
        sql_lines.append(f"""
-- Страница Контакты
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '{escape_sql_string(contacts_content)}', 'Контакты', '', 'publish', 'closed', 'closed', '', 'kontakty', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # 5. Страница История (если есть)
    if data.get('history'):
        sql_lines.append(f"""
-- Страница История храма
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '<h2>История храма</h2>{escape_sql_string(data["history"].get("content", ""))}', 'История храма', '', 'publish', 'closed', 'closed', '', 'istoriya-hrama', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # 6. Страница Деятельность (если есть)
    if data.get('activities'):
        activities_content = "<h2>Деятельность прихода</h2>"
        for activity in data['activities']:
            activities_content += f"""
            <div class='feature-card scroll-reveal'>
                <h3>{escape_sql_string(activity.get('title', ''))}</h3>
                {escape_sql_string(activity.get('content', ''))}
            </div>
            """
        
        sql_lines.append(f"""
-- Страница Деятельность
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({page_id}, 1, NOW(), NOW(), '{escape_sql_string(activities_content)}', 'Деятельность прихода', '', 'publish', 'closed', 'closed', '', 'deyatelnost', '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0);
""")
        page_id += 1
    
    # Добавляем новости как посты
    if data.get('main', {}).get('news'):
        sql_lines.append("\n-- Новости (посты)\n")
        category_id = 1  # ID категории "Новости"
        
        # Создаем категорию Новости если нет
        sql_lines.append("""
-- Категория Новости
INSERT IGNORE INTO wp_terms (name, slug, term_group) VALUES ('Новости', 'novosti', 0);
INSERT IGNORE INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ((SELECT term_id FROM wp_terms WHERE slug = 'novosti'), 'category', '', 0, 0);
""")
        
        post_id = 200  # Начальный ID для постов
        for news in data['main']['news'][:20]:  # Максимум 20 новостей
            news_content = escape_sql_string(news.get('content', news.get('title', '')))
            news_title = escape_sql_string(news.get('title', 'Новость'))
            news_date = news.get('date', '')
            
            sql_lines.append(f"""
INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES
({post_id}, 1, NOW(), NOW(), '{news_content}', '{news_title}', '', 'publish', 'open', 'open', '', '{create_slug(news_title)}', '', '', NOW(), NOW(), '', 0, '', 0, 'post', '', 0);
""")
            
            # Связь с категорией
            sql_lines.append(f"""
INSERT INTO wp_term_relationships (object_id, term_taxonomy_id, term_order) VALUES ({post_id}, (SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE taxonomy = 'category' AND term_id = (SELECT term_id FROM wp_terms WHERE slug = 'novosti')), 0);
""")
            
            post_id += 1
    
    # Меню навигации
    sql_lines.append("\n\n-- Настройка меню (создайте вручную в админке или используйте этот SQL)\n")
    sql_lines.append("""
-- Пример создания меню (требует дополнительной настройки в админке)
-- Appearance > Menus > Создать меню "Главное меню"
-- Добавить страницы: Главная, Расписание богослужений, Духовенство, Контакты, История
""")
    
    # Записываем в файл
    with open(OUTPUT_SQL, 'w', encoding='utf-8') as f:
        f.write('\n'.join(sql_lines))
    
    print(f"\nSQL файл создан: {OUTPUT_SQL}")
    return True

def main():
    """Основная функция"""
    print("=" * 60)
    print("Парсинг сайта alexander-nevskiysobor.ru")
    print("=" * 60)
    
    all_data = {}
    
    # 1. Главная страница
    main_html = fetch_page(BASE_URL)
    if main_html:
        all_data['main'] = parse_main_page(main_html)
        print(f"✓ Главная страница: найдено {len(all_data['main'].get('news', []))} новостей")
    
    # 2. Расписание - пробуем разные варианты URL
    schedule_urls = [
        BASE_URL + "/%D1%80%D0%B0%D1%81%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B1%D0%BE%D0%B3%D0%BE%D1%81%D0%BB%D1%83%D0%B6%D0%B5%D0%BD%D0%B8%D0%B9/",  # расписание-богослужений
        BASE_URL + "/raspisanie-bogosluzhenij/",
        BASE_URL + "/schedule/"
    ]
    for url in schedule_urls:
        html = fetch_page(url)
        if html:
            all_data['schedule'] = parse_schedule_page(html)
            print(f"✓ Расписание загружено с: {url}")
            break
    
    # 3. Духовенство
    clergy_urls = [
        BASE_URL + "/category/%d0%bf%d0%b5%d1%80%d1%81%d0%be%d0%bd%d0%b0%d0%bb%d0%b8%d0%b8-%d1%81%d0%be%d0%b1%d0%be%d1%80%d0%b0/",  # персоналии-собора
        BASE_URL + "/duhovenstvo/",
        BASE_URL + "/clergy/"
    ]
    for url in clergy_urls:
        html = fetch_page(url)
        if html:
            all_data['clergy'] = parse_clergy_page(html)
            print(f"✓ Духовенство: найдено {len(all_data.get('clergy', []))} записей")
            break
    
    # 4. Контакты
    contacts_urls = [
        BASE_URL + "/%d0%ba%d0%be%d0%bd%d1%82%d0%b0%d0%ba%d1%82%d1%8b/",  # контакты
        BASE_URL + "/kontakty/",
        BASE_URL + "/contacts/"
    ]
    for url in contacts_urls:
        html = fetch_page(url)
        if html:
            all_data['contacts'] = parse_contacts_page(html)
            print(f"✓ Контакты загружены")
            break
    
    # 5. О соборе (история)
    history_urls = [
        BASE_URL + "/%d0%be-%d1%81%d0%be%d0%b1%d0%be%d1%80%d0%b5/",  # о-соборе
        BASE_URL + "/istoriya/",
        BASE_URL + "/about/"
    ]
    for url in history_urls:
        html = fetch_page(url)
        if html:
            all_data['history'] = parse_history_page(html)
            print(f"✓ История храма загружена")
            break
    
    # 6. Деятельность
    activities_urls = [
        BASE_URL + "/deyatelnost/",
        BASE_URL + "/activities/",
        BASE_URL + "/services/"
    ]
    for url in activities_urls:
        html = fetch_page(url)
        if html:
            all_data['activities'] = parse_activities_page(html)
            print(f"✓ Деятельность: найдено {len(all_data['activities'])} разделов")
            break
    
    # Генерируем SQL файл
    if all_data:
        generate_sql_file(all_data)
        print("\n" + "=" * 60)
        print("Готово! SQL файл создан.")
        print("Теперь выполните этот SQL файл в базе данных WordPress.")
        print("=" * 60)
    else:
        print("Не удалось получить данные с сайта.")

if __name__ == "__main__":
    main()
