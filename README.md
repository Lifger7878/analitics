# Аналітика ліцензування

Laravel 12 dashboard for transport licensing analytics by Ukrainian regions.

## Запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Відкрийте `http://127.0.0.1:8000`.

Якщо PHP і Composer не встановлені локально:

```bash
docker compose up
```

## Структура

- `routes/web.php` - головний маршрут.
- `app/Http/Controllers/AnalyticsController.php` - статистика та підготовка даних.
- `resources/views/analytics/index.blade.php` - Blade-інтерфейс.
- `resources/data/map.json` - геометрія карти 26 областей.
- `public/css/analytics.css` - стилі.
- `public/js/analytics.js` - інтерактивність карти та панелі.
# analitics
