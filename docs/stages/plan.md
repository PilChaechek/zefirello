# 🗺️ Roadmap: Kanban System (Feature-Driven)

## 🏁 Этап 1: Фундамент (Environment)
*Подготовка среды, чтобы можно было писать код.*
1.  **System:** Docker (Sail), MySQL, Redis.
2.  **Backend:** Laravel 12 Install, Git Init, Config, Code Quality (Pint).
3.  **Frontend:** Vue 3 Init, Tailwind v4, TypeScript Config, shadcn-vue.

---

## 🔐 Этап 2: Аутентификация (Authentication Feature)
*Фича: "Я как пользователь хочу регистрироваться и входить в систему, чтобы видеть свои данные".*
1.  **Database:** Таблица `users` (добавим `avatar_path` сразу).
2.  **Backend:**
    *   Настройка `laravel/sanctum` (SPA Config).
    *   `AuthController` (Login, Register, Logout, Me).
    *   `UserResource` (API Response).
3.  **Frontend:**
    *   Настройка `Axios` (Interceptors, Credentials).
    *   `useAuthStore` (Pinia).
    *   Страницы Login/Register (Forms + Zod Validation).
    *   Layouts: GuestLayout vs AppLayout.

---

## 📋 Этап 3: Управление Досками (Boards Feature)
*Фича: "Я хочу создавать пространства (доски) для работы".*
1.  **Database:** Таблица `boards` (user_id, name, color).
2.  **Backend:**
    *   `BoardController` (CRUD).
    *   `CreateBoardAction` (Logic).
    *   `BoardResource`.
3.  **Frontend:**
    *   Dashboard Page (Список досок).
    *   Modal: "Create Board" (Dialog component).
    *   Routing: Переход внутрь доски (`/boards/{id}`).

---

## 🏗️ Этап 4: Структура Доски (Columns Feature)
*Фича: "Я хочу разделять задачи по статусам (Колонки) и менять их порядок".*
1.  **Database:** Таблица `columns` (board_id, title, sort_order).
2.  **Backend:**
    *   `spatie/eloquent-sortable` integration.
    *   `ColumnController` (Store, Update, Destroy).
    *   Nested Eager Loading: Board -> Columns.
3.  **Frontend:**
    *   Board View (Горизонтальный скролл).
    *   Компонент `BoardColumn`.
    *   Создание колонки "на лету".

---

## 📝 Этап 5: Задачи (Tasks CRUD Feature)
*Фича: "Я хочу создавать карточки задач".*
1.  **Database:** Таблица `tasks` (column_id, title, description, position).
2.  **Backend:**
    *   `TaskController`.
    *   `CreateTaskAction`.
3.  **Frontend:**
    *   Компонент `TaskCard`.
    *   Модальное окно просмотра задачи.
    *   Zod схема для валидации создания.

---

## 🔄 Этап 6: Механика Канбан (Drag & Drop Feature)
*Фича: "Я хочу перетаскивать задачи между колонками".*
1.  **Frontend:**
    *   Интеграция `vue-draggable-plus`.
    *   Логика optimistic UI (визуальное обновление до ответа сервера).
2.  **Backend:**
    *   `MoveTaskAction` (Сложная логика пересчета позиций).
    *   Batch Update (оптимизация SQL запросов).

---

## ✍️ Этап 7: Rich Content (Editor Feature)
*Фича: "Я хочу красиво описывать задачу (жирный текст, списки)".*
1.  **Frontend:**
    *   Интеграция `Tiptap` (Headless Editor).
    *   Стилизация редактора под shadcn-vue.
2.  **Backend:**
    *   Sanitization (Очистка HTML от XSS перед сохранением).

---

## 📎 Этап 8: Медиа (Attachments Feature)
*Фича: "Я хочу прикреплять картинки к задачам".*
1.  **Database:** Таблица `media` (через пакет Spatie).
2.  **Backend:**
    *   Установка `spatie/laravel-medialibrary`.
    *   API Endpoint: `UploadAttachment`.
3.  **Frontend:**
    *   Drag & Drop зона загрузки в модалке задачи.
    *   Галерея вложений.

---

## 🚀 Этап 9: Финализация (Polish & Deploy)
1.  **Refactor:** Проверка N+1 запросов, Code Style.
2.  **UX:** Global Toast Notifications (`vue-sonner`).
3.  **Deploy:** Сборка Production билда.
