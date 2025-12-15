// resources/js/types/user.ts

export interface Role {
    name: string;
    label: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    roles: Role[];
}
