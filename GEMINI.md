# GEMINI.md

## Обзор проекта

**Zefirello** — это веб-приложение для управления задачами и проектами, разработанное на фреймворке Laravel (бэкенд) и Vue.js (фронтенд). Система предназначена для организации совместной работы и включает управление пользователями, задачами, проектами и ролями.

**Среда разработки:**

*   **ОС:** Windows Subsystem for Linux (WSL)
*   **Контейнеризация:** Docker
*   **Инструментарий:** Laravel Sail (используется алиас `sail`)
*   **VCS:** Git

**Бэкенд:**

*   **Фреймворк:** Laravel 12.41.1
*   **Язык:** PHP 8.2+
*   **База данных:** MySQL 8.0+
*   **Кэширование и очереди:** Redis
*   **Качество кода:** Laravel Pint (PSR-12)
*   **Аутентификация:** Laravel Sanctum для аутентификации API
*   **Авторизация:** Spatie Laravel Permission для управления доступом на основе ролей (RBAC)
*   **API:** RESTful API с версионированием (`/api/v1`)

**Фронтенд:**

*   **Фреймворк:** Vue.js 3.5.25
*   **Язык:** TypeScript 5.9.3
*   **Инструмент сборки:** Vite 7.2.7 (Laravel Plugin v2.0.1)
*   **Стилизация:** Tailwind CSS 4.0.0 (с плагином `tailwindcss-animate`)
*   **Управление состоянием:** Pinia 3.0.4
*   **Маршрутизация:** Vue Router 4.6.3
*   **UI-библиотека:** **shadcn-vue** (https://www.shadcn-vue.com/)
    *   Основана на Reka UI (headless) и Tailwind CSS.
    *   Используются иконки Lucide Vue Next.
*   **Утилиты и Валидация:**
    *   VueUse 14.1.0
    *   Vee-Validate 4.15.1 (с Zod)
    *   Axios 1.13.2

## Сборка и запуск (WSL + Docker)
Все команды выполняются внутри среды WSL с использованием алиаса **sail**.

**Настройка:**

1.  Скопируйте файл окружения:
    ```bash
    cp .env.example .env
    ```
2.  Запустите контейнеры (в фоновом режиме):
    ```bash
    sail up -d
    ```
3.  Установите зависимости и выполните миграции:
    ```bash
    sail composer install
    sail npm install
    sail artisan key:generate
    sail artisan migrate
    ```

**Разработка:**

Для работы над проектом используйте следующие команды:

1.  **Запуск бэкенда (Laravel):**
    Если контейнеры еще не запущены:
    ```bash
    sail up -d
    ```
    *   **Backend URL:** http://localhost:8001

2.  **Запуск фронтенда (Vite):**
    Сервер разработки фронтенда запускается через Sail:
    ```bash
    sail npm run dev
    ```
*   **Frontend URL:** http://localhost:5174

**Тестирование:**

Запуск тестов осуществляется также через Sail:

```bash
sail composer run test
# или
sail artisan test
```

## Соглашения о разработке

*   **Среда выполнения (Sail):** Все команды CLI (composer, artisan, npm) должны выполняться через команду `sail`.
    *   Пример: `./vendor/bin/sail artisan make:model Project`
    *   Пример: `./vendor/bin/sail npm install`
*   **TypeScript:** Весь код фронтенда должен быть написан на **TypeScript**.
    *   В компонентах Vue используйте `<script setup lang="ts">`.
    *   Описывайте интерфейсы/типы для пропсов, ответов API и состояния Pinia.
    *   Избегайте использования `any`, стремитесь к строгой типизации.
*   **Стиль кода (Бэкенд):** Используется **Laravel Pint** для форматирования кода в соответствии со стандартом PSR-12.
*   **API:** API версионируется и следует принципам RESTful.
* **Backend Structure (Enterprise Standard):**
    *   **Controllers:** Максимально "тонкие". валидация (`FormRequest`) и возврат ответа (`JsonResource`).
*   **Взаимодействие с API (Axios):** `baseURL` для axios глобально настроен на `/api/v1`.
    *   При написании запросов к API **не указывайте** префикс `api/v1`.
    *   **Правильно:** `const { data } = await axios.get<User[]>('/users');` (с указанием типа ответа)
    *   **Неправильно:** `const { data } = await axios.get('/api/v1/users');`
*   **Аутентификация:** Аутентификация API обрабатывается с помощью Laravel Sanctum.
*   **Авторизация:** Контроль доступа на основе ролей реализован с использованием библиотеки Spatie Laravel Permission.
*   **Фронтенд:** Фронтенд представляет собой одностраничное приложение (SPA), созданное на Vue.js 3.5+. Используется компонентная архитектура (Composition API) и централизованная система управления состоянием (Pinia).
*   **Стилизация:** В проекте используется Tailwind CSS v4 — utility-first CSS-фреймворк.
*   **База данных:** Изменения схемы базы данных управляются через миграции Laravel.
*   **Git:** Сообщения коммитов следуют спецификации Conventional Commits, но пишутся на русском языке с обязательным указанием области (scope) в скобках.
    *   **Формат:** тип(область): описание
    *   **Пример:** feat(projects): добавлен бэкенд и фронтенд фундамент для проектов
    *   **Пример:** fix(projects): исправить ошибку сортировки таблицы
    *   **Пример:** refactor(api): версионирование API ресурсов
* В проекте используется темная и светлая тема, нужно учитывать классы tailwind
## Структура проекта

*   **Бэкенд:**
    *   **Контроллеры:** `app/Http/Controllers/Api/V1`
        *   Группируются по модулям (например, `Auth`, `Project`, `User`).
    *   **Реквесты (FormRequests):** `app/Http/Requests/Api/V1`
        *   Группируются по модулям (например, `User/StoreUserRequest.php`).
    *   **Ресурсы (API Resources):** `app/Http/Resources`
        *   Группируются по сущностям (например, `User/UserResource.php`).
    *   **Модели:** `app/Models` (например, `User.php`, `Project.php`).
    *   **Политики:** `app/Policies` (например, `ProjectPolicy.php`).
    *   **Провайдеры:** `app/Providers`.
*   **Фронтенд (`resources/js`):**
    *   **Входная точка:** `app.ts`, `App.vue`, `bootstrap.ts`
    *   **UI Компоненты (shadcn-vue):** `components/ui`
    *   **Глобальные компоненты лейаута:** `components/layout` (хедеры, сайдбары)
    *   **Лейауты страниц:** `layouts` (например, `AppLayout.vue`)
    *   **Страницы (Views):** `views`
        *   Структура модульная: `views/{module}/` (например, `views/projects/`).
        *   Файлы страниц: `views/{module}/*View.vue` (например, `ProjectIndexView.vue`).
        *   Локальные компоненты модуля: `views/{module}/components/`.
    *   **Утилиты:** `lib` (например, `utils.ts`, `zod-ru.ts`)
    *   **Сторы (Pinia):** `stores`
    *   **Типы/Интерфейсы:** `types`
    *   **Роутер:** `router`
