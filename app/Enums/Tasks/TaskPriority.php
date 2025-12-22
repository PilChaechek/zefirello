<?php

namespace App\Enums\Tasks;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Низкий',
            self::MEDIUM => 'Средний',
            self::HIGH => 'Высокий',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOW => 'ArrowDown',
            self::MEDIUM => 'ArrowRight',
            self::HIGH => 'ArrowUp',
        };
    }
}
