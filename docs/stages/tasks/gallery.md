# План реализации универсальной системы вложений (бэкенд)

**Цель:** Создать гибкую систему для загрузки и управления файлами (изображения, документы и т.д.), которая может быть привязана к разным сущностям (задачи, а в будущем — заметки и др.). Система будет использовать полиморфные связи для масштабируемости.

---

### Шаг 1: Настройка базы данных и моделей (Полиморфная структура)

1.  **Настроить Morph Map:**
    *   **Действие:** В файле `app/Providers/AppServiceProvider.php`, в методе `boot()`, зарегистрировать карту полиморфных отношений.
    *   **Причина:** Это позволяет использовать в базе данных короткие псевдонимы (`task`) вместо полных имен классов (`App\Models\Task`), что делает систему устойчивой к перемещению или переименованию моделей.
    *   **Код:**
        ```php
        use Illuminate\Database\Eloquent\Relations\Relation;

        public function boot(): void
        {
            Relation::morphMap([
                'task' => \App\Models\Task::class,
                // 'note' => \App\Models\Note::class, // для будущих сущностей
            ]);
        }
        ```

2.  **Создать миграцию для `attachments`:**
    *   **Действие:** Сгенерировать файл миграции для создания таблицы `attachments`.
    *   **Поля таблицы:**
        *   `id` (primary key)
        *   `attachable_id` (unsignedBigInteger, ID родительской сущности)
        *   `attachable_type` (string, псевдоним родительской сущности, например, `task`)
        *   `user_id` (foreign key, `users.id`, кто загрузил)
        *   `path` (string, путь к файлу в хранилище, например, `attachments/image.jpg`)
        *   `original_name` (string, исходное имя файла)
        *   `mime_type` (string, тип файла, например, `image/jpeg`, `application/pdf`)
        *   `size` (integer, размер в байтах)
        *   `timestamps` (created\_at, updated\_at)
    *   **Важно:** Добавить составной индекс для полей `[attachable_id, attachable_type]`.

3.  **Создать модель `Attachment`:**
    *   **Действие:** Сгенерировать модель `App\Models\Attachment`.

4.  **Описать отношения:**
    *   В модели `Task` добавить метод `attachments()`, возвращающий полиморфное отношение `morphMany` к `Attachment`.
    *   В модели `Attachment` добавить метод `attachable()`, возвращающий отношение `morphTo`, и `user()` для связи с `User`.

---

### Шаг 2: Настройка файлового хранилища

1.  **Проверить конфигурацию:** Убедиться, что в `config/filesystems.php` диск `public` настроен правильно для публичного доступа.
2.  **Создать символическую ссылку:** Выполнить команду `sail artisan storage:link`, чтобы сделать директорию `storage/app/public` доступной из веба. **Это нужно сделать один раз.**

---

### Шаг 3: Реализация API

1.  **Создать контроллеры:**
    *   `TaskAttachmentController` в `app/Http/Controllers/Api/V1/` для привязки вложений к задачам.
    *   `AttachmentController` в `app/Http/Controllers/Api/V1/` для общих действий, таких как удаление.

2.  **Определить маршруты (в `routes/api.php`):**
    *   `POST /tasks/{task}/attachments` -> `TaskAttachmentController@store` (для загрузки файлов к конкретной задаче).
    *   `DELETE /attachments/{attachment}` -> `AttachmentController@destroy` (для удаления любого вложения по его ID).

3.  **Реализовать логику в контроллерах:**
    *   **`store`** (в `TaskAttachmentController`):
        *   Принять `UploadedFile` из запроса.
        *   Сохранить файл: `Storage::disk('public')->put('attachments', $file)`.
        *   Создать запись в таблице `attachments`, используя полиморфную связь с моделью `Task`.
        *   Вернуть `AttachmentResource` с данными о созданном вложении.
    *   **`destroy`** (в `AttachmentController`):
        *   Найти модель `Attachment`.
        *   Удалить файл из хранилища: `Storage::disk('public')->delete($attachment->path)`.
        *   Удалить запись из БД.
        *   Вернуть успешный ответ (HTTP 204 No Content).

4.  **Валидация и Авторизация:**
    *   **Request:** Создать `StoreAttachmentRequest` для валидации (тип файла `mimes`, размер `max`).
    *   **Policy:** Создать `AttachmentPolicy` для проверки прав.
        *   `destroy`: убедиться, что пользователь может удалять вложение (он автор или менеджер проекта).
        *   Для `store` проверку прав делегировать политике родительской сущности (например, `TaskPolicy` должен проверять, может ли юзер добавлять вложения к задаче).

5.  **API-ресурсы:**
    *   Создать `AttachmentResource` для форматирования ответа (добавление полного URL к файлу через `Storage::url($this->path)`).
    *   В `TaskResource` добавить подгрузку и форматирование коллекции `attachments` с использованием `AttachmentResource`.

---

### Шаг 4: Наполнение тестовыми данными (Seeder)

1.  **Создать сидер:** Сгенерировать `AttachmentSeeder`.
2.  **Написать логику сидера:**
    *   Получить несколько существующих задач.
    *   В цикле для каждой задачи:
        *   Убедиться, что директория `storage/app/public/attachments` существует.
        *   Используя HTTP-клиент Laravel, скачать 2-3 случайных изображения с сервиса-плейсхолдера (например, `https://picsum.photos`).
        *   Сохранить файлы в `storage/app/public/attachments`.
        *   Создать записи в таблице `attachments`, связав их с текущей задачей через полиморфную связь.
3.  **Зарегистрировать сидер:** Добавить вызов `AttachmentSeeder` в `DatabaseSeeder.php`.
