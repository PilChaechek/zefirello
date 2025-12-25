# Принципы разделения UI для ролей (Frontend)

Этот документ описывает универсальный и масштабируемый подход к управлению видимостью элементов интерфейса в зависимости от роли текущего пользователя.

**Основной принцип:** Бэкенд является единственным источником правды для **безопасности**. Фронтенд отвечает только за **пользовательский опыт (UX)**, скрывая элементы, с которыми пользователь все равно не сможет взаимодействовать.

## 1. Единый Источник Правды: Pinia Store

Всегда должен существовать централизованный стейт-менеджер, который хранит информацию о текущем пользователе, включая его роли и разрешения. В нашем проекте это `stores/auth.ts`.

```typescript
// stores/auth.ts
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    roles: [] as string[], // e.g., ['admin', 'manager']
    permissions: [] as string[], // e.g., ['create projects', 'delete users']
  }),
  // ...
  getters: {
    // Наши универсальные инструменты для проверки прав в любом компоненте
    hasRole: (state) => (role: string) => state.roles.includes(role),
    can: (state) => (permission: string) => state.permissions.includes(permission),
  }
})
```
Этот стор — наш "паспортный стол". Любой компонент может обратиться к нему, чтобы проверить права.

## 2. Три универсальных способа применения

В зависимости от задачи, используется один из трех паттернов.

### Способ 1: Простое скрытие элементов (`v-if`)

Это самый частый и простой способ (90% случаев). Используется для скрытия отдельных кнопок, пунктов меню, полей формы и т.д.

**Пример (кнопка "Создать проект"):**
```vue
// Любой компонент, например, ProjectIndexView.vue
<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
const authStore = useAuthStore();
</script>

<template>
    <!-- Кнопка будет отрендерена только если hasRole('admin') вернет true -->
    <Button v-if="authStore.hasRole('admin')" @click="openCreateDialog">
        Добавить проект
    </Button>
</template>
```

### Способ 2: Динамическое изменение структуры (`computed`)

Используется, когда нужно не просто скрыть элемент, а **изменить структуру данных** (например, отфильтровать массив) перед передачей в дочерний компонент.

**Пример (скрытие колонки "Действия" в таблице):**
Мы не можем поставить `v-if` на саму колонку, так как компонент `<DataTable>` принимает готовый массив колонок. Поэтому мы создаем `computed`-свойство, которое возвращает уже отфильтрованный массив.

```vue
// ProjectIndexView.vue
<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { columns as originalColumns } from './components/columns';

const authStore = useAuthStore();

// `computed` реактивно отфильтрует массив
const columns = computed(() => {
    const isAdmin = authStore.hasRole('admin');
    // Если пользователь не админ, колонка с id: 'actions' будет удалена из массива
    return originalColumns.filter(col => isAdmin || col.id !== 'actions');
});
</script>

<template>
    <!-- В DataTable попадет уже правильный, отфильтрованный массив -->
    <DataTable :columns="columns" :data="projects" />
</template>
```

### Способ 3: Защита целых страниц (Vue Router)

Используется, чтобы полностью заблокировать доступ к определенной странице (роуту), например, `/admin/users`. Это делается с помощью глобальных навигационных хуков (`navigation guards`).

**Пример (`router/index.ts`):**
```typescript
// router/index.ts
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  routes: [
    {
      path: '/users',
      name: 'admin.users',
      component: () => import('@/views/users/UserIndexView.vue'),
      // Добавляем мета-поле с требованием
      meta: { requiresRole: 'admin' }
    }
  ]
});

// Перед каждым переходом роутер будет выполнять эту проверку
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const requiredRole = to.meta.requiresRole as string | undefined;

  // Если у пользователя нет требуемой роли
  if (requiredRole && !authStore.hasRole(requiredRole)) {
    // Перенаправляем его на страницу с ошибкой доступа или на главную
    next({ name: 'forbidden' });
  } else {
    // В противном случае — разрешаем переход
    next();
  }
});
```
---

Эти три способа, основанные на едином сторе `Pinia`, покрывают все потребности в управлении видимостью UI в зависимости от прав пользователя и являются универсальным решением для всего проекта.
