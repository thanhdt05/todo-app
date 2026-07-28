<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case DOING = 'doing';
    case DONE = 'done';

    public static function label(string $status): string
    {
        return match ($status) {
            self::TODO->value => 'Chưa làm',
            self::DOING->value => 'Đang làm',
            self::DONE->value => 'Hoàn thành',
        };
    }

    public static function options(): array
    {
        return [
            ['value' => self::TODO->value, 'label' => self::label(self::TODO->value)],
            ['value' => self::DOING->value, 'label' => self::label(self::DOING->value)],
            ['value' => self::DONE->value, 'label' => self::label(self::DONE->value)],
        ];
    }

    public static function isCompleted(string $status): bool
    {
        return $status === self::DONE->value;
    }
}
