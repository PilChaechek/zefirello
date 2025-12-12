```text
resources/js/
├── assets/                  # Картинки (svg, png)
├── components/              # ВСЕ компоненты
│   ├── ui/                  # Shadcn (Button, Input, Sidebar)
│   ├── layout/              # Твои "умные" части лейаута (AppSidebar, UserNav)
│   └── common/              # Общие компоненты (Loader, ErrorMessage)
├── layouts/                 # Обертки страниц (AppLayout, GuestLayout)
├── lib/                     # Утилиты (utils.ts)
├── router/                  # Роутер
├── stores/                  # Pinia
└── views/                   # Страницы (То, куда ведет роутер)
    ├── auth/                # LoginView.vue, RegisterView.vue
    ├── home/                # HomeView.vue (или просто в корней views)
    └── tasks/               # TasksView.vue, KanbanView.vue
```
