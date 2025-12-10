## Backend
- Framework: Laravel 12 (API Mode)
- Language: PHP 8.2+
- Database: MySQL 8.0+
- DevOps: Docker, Docker Compose (через Laravel Sail), WSL 2.
- WSL для локальной разработки
- Code Quality: Laravel Pint (PSR-12).
- Кэширование, управление очередями `Redis`

## Frontend (Vue 3 SPA)
- `Vue 3` (Composition API `<script setup>`).
- Typescript
- Build: `Vite`.
- Router: `Vue Router`.
- State Management: `Pinia` (Store) https://github.com/vuejs/pinia
- HTTP Client: `Axios`
- Code Quality: `ESLint + Prettier`

## UI/UX & Styling
- `TailwindCSS` v4
- Component Library: `shadcn-vue` https://github.com/unovue/shadcn-vue
- Иконки: ` lucide-vue-next` https://github.com/lucide-icons/lucide
- Content Editor: Tiptap (Headless WYSIWYG редактор для описания задач) https://github.com/ueberdosis/tiptap
- Notifications `vue-sonner` https://github.com/xiaoluoboding/vue-sonner

## пакеты Laravel
- Авторизация `laravel/sanctum` (Cookie-based аутентификация для SPA).
- Роли: `spatie/laravel-permission` (Роли: Admin, User).
- Media: `spatie/laravel-medialibrary` (Для вложений и картинок в карточках).
- Sortable: `spatie/eloquent-sortable` (Логика сортировки карточек Kanban). https://github.com/spatie/eloquent-sortable

## Vue библиотеки
- Utilities: `VueUse (@vueuse/core)` https://vueuse.org/ (useTimeAgo,onClickOutside,useTitle)
- Dates: `dayjs` (Работа с датами и временем) https://github.com/iamkun/dayjs/ 
- Validation: zod + vee-validate (Схемная валидация форм).
- Drag & Drop: `vue-draggable-plus` https://github.com/Alfred-Skyblue/vue-draggable-plus

## Рабочее окружение
- PhpStorm 2025.1.4.1
- API Testing: Postman
- VCS: Git
