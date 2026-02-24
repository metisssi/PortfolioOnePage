# 🏥 Léčba bolestí zad — PHP Backend

## Что изменилось

| Было (Node.js) | Стало (PHP) |
|---|---|
| Express.js сервер | Apache + PHP |
| MongoDB | MySQL |
| Нужен VPS (~56€/год) | Shared hosting (~25-45 Kč/мес) |
| npm install, node server.js | Просто загрузить файлы по FTP |

## Структура файлов на хостинге

```
public_html/              ← корневая папка хостинга
├── api/
│   ├── .htaccess         ← роутинг API
│   ├── config.php        ← ⚠️ НАСТРОИТЬ! БД + пароли
│   ├── cors.php
│   ├── auth.php
│   ├── content.php
│   ├── gallery.php
│   ├── reviews.php
│   ├── health.php
│   └── install.php       ← ⚠️ УДАЛИТЬ после установки!
├── .htaccess             ← роутинг фронтенда
├── index.html            ← главная страница
├── admin.html            ← админ панель
├── src/
│   ├── style.css
│   ├── main.js
│   ├── utils/
│   │   ├── config.js     ← ⚠️ ИЗМЕНИТЬ API URL
│   │   └── nav.js
│   └── pages/
│       ├── sluzby.js
│       ├── gallery.js
│       └── reviews.js
└── assets/
    ├── hero-bg.jpg
    ├── mainpicture1.JPG
    └── mainpicture2.JPG
```

## Инструкция по деплою

### 1. Купить хостинг
Любой shared хостинг с PHP + MySQL (Active24, Czechia, Wedos и т.д.)

### 2. Создать базу данных MySQL
В панели хостинга создай БД и запиши:
- Имя базы данных
- Имя пользователя
- Пароль

### 3. Настроить config.php
Открой `api/config.php` и измени:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tvoje_databaze');      // ← имя БД
define('DB_USER', 'tvuj_uzivatel');       // ← пользователь
define('DB_PASS', 'tvoje_heslo');         // ← пароль БД
define('ADMIN_PASSWORD', 'admin123');     // ← пароль для админки
define('JWT_SECRET', 'random_string');    // ← случайная строка
```

### 4. Настроить config.js (фронтенд)
В `src/utils/config.js` поменяй:
```js
export const API = '/api';
```

### 5. Загрузить файлы
Загрузи ВСЕ файлы по FTP в папку `public_html` (или `www`)

### 6. Запустить установку
Открой в браузере: `https://bolesti-zad-lecba.cz/api/install.php`
Должно появиться:
```
✅ Tabulka 'content' vytvořena
✅ Tabulka 'gallery' vytvořena
✅ Tabulka 'reviews' vytvořena
✅ Počáteční data vložena
🎉 Instalace dokončena!
```

### 7. УДАЛИТЬ install.php!
Обязательно удали `api/install.php` после установки — это важно для безопасности.

### 8. Привязать домен
В Czechia DNS настройках для `bolesti-zad-lecba.cz`:
- Пропиши A-запись на IP хостинга (узнай в панели хостинга)

### 9. Проверить
- Сайт: `https://bolesti-zad-lecba.cz`
- Админка: `https://bolesti-zad-lecba.cz/admin.html`
- API: `https://bolesti-zad-lecba.cz/api/health`

## Локальная разработка (опционально)
```bash
# PHP built-in сервер
cd путь/к/проекту
php -S localhost:8000

# В config.js поменять:
export const API = 'http://localhost:8000/api';
```
