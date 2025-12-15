## Этап 1: Backend — Получение данных (Read)
- API Resource: Создать UserResource для преобразования данных (скрыть пароли, отформатировать даты).
- Controller (Index): Реализовать метод index в UserController с пагинацией.
- Route: Зарегистрировать защищенный маршрут api/users.

## Этап 2: Frontend — Отображение списка (Read)
Подключаем Vue к API.
- TypeScript Interface: Описать тип IUser, чтобы IDE помогала нам (Intellisense).
- Service Layer: Создать services/userService.ts (или useUsers composable) для запросов к API. Не пишем axios.get внутри компонентов!
- UI Table: Реализовать таблицу в UserIndexView.vue.
- Challenge: Сделать пагинацию (кнопки "Назад/Вперед").

## Этап 3: Backend — Создание (Create)
- Request Validation: Создать StoreUserRequest. Валидация — это ответственность бэкенда (email unique, password rules).
- Controller (Store): Реализовать метод store.

## Этап 4: Frontend — Форма создания (Create)
- UI Form: Создать кнопку "Добавить" и форму (в модалке Dialog или на отдельной странице).
- Form Validation: Прикрутить Zod + VeeValidate. (Валидируем и на фронте для UX, и на бэке для безопасности).

## Этап 5: Редактирование и Удаление (Update & Delete)
- Backend: Методы show (получить одного), update (сохранить) и destroy (удалить).
- Frontend: Страница/Модалка редактирования и кнопка удаления с подтверждением ("Вы уверены?").
