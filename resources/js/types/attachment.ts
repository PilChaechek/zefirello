export interface Attachment {
    id: number;
    url: string;
    thumbnail_url?: string;
    original_name: string;
    mime_type: string;
    size: number;
    created_at: string;
    user_id: number;
}
