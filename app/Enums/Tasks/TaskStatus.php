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

    public function icon(): string
    {
        return match ($this) {
            self::TODO => 'Circle',
            self::IN_PROGRESS => 'Timer',
            self::DONE => 'CircleCheck',
            self::CANCELED => 'CircleX',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TODO => 'text-gray-500',
            self::IN_PROGRESS => 'text-blue-500',
            self::DONE => 'text-green-500',
            self::CANCELED => 'text-red-500',
        };
    }
}
