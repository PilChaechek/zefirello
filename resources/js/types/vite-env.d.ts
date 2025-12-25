/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_BACKEND_URL: string;
    // Добавьте сюда другие переменные окружения, которые вы используете с префиксом VITE_
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
