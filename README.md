# Отзывы из Яндекс.Карт (Laravel + Vue)

https://khabdev.site

email: khabdev@example.com
password: hfyljvysqgfhjkm

Короткая инструкция по установке и запуску проекта.

## Требования

- PHP 8.2+
- Composer 2
- Node.js 18+ и npm
- SQLite/MySQL/PostgreSQL (по умолчанию SQLite)

## Установка

1. Клонировать репозиторий и перейти в папку проекта

```bash
git clone https://github.com/HKreoin/testtask.git
cd testtask
```

2. Установить бекенд‑зависимости

```bash
composer install
```

3. Создать файл окружения и сгенерировать ключ приложения

```bash
cp .env.example .env
php artisan key:generate
```

4. Настроить подключение к БД в `.env`, по умолчанию SQLite

5. Накатить миграции

```bash
php artisan migrate
```

6. Установить фронтенд‑зависимости и запустить сборку

```bash
npm install
npm run build
```

7. Запустить Laravel сервер

```bash
php artisan serve
```

Откройте в браузере адрес из вывода (обычно `http://127.0.0.1:8000`).

## Первичный вход и настройка

1. Зарегистрируйтесь/войдите в систему.
2. Перейдите в раздел «Настройки».
3. Вставьте ссылку на страницу отзывов организации в Яндекс.Картах, например:
   `https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/`
4. Нажмите «Сохранить».
5. Перейдите в «Отзывы» — увидите последние 10 отзывов и сводку по рейтингу.

Примечание: поле ввода URL автоматически выделяет весь текст при клике.

## Как это работает

- Сервис получает HTML страницы по HTTP и парсит DOM‑селекторами (без JSON‑LD).
- Результаты кэшируются на 10 минут.

## Очистка кэша

```bash
php artisan cache:clear
```

## Скрипты (полезно)

Полный цикл (после клонирования):

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```
