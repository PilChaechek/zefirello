<?php

namespace App\Enums\Tasks;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case CANCELED = 'canceled';
    public function label(): string
    {
        return match ($this) {
            self::TODO => 'К выполнению',
            self::IN_PROGRESS => 'В работе',
            self::DONE => 'Готово',
            self::CANCELED => 'Отменено',
        };
    }
}
