<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function label(string $priority): string
    {
        return match ($priority) {
            self::LOW->value => 'Thấp',
            self::MEDIUM->value => 'Trung bình',
            self::HIGH->value => 'Cao',
            default => 'Trung bình',
        };
    }

    public static function options(): array
    {
        return [
            ['value' => self::LOW->value, 'label' => self::label(self::LOW->value)],
            ['value' => self::MEDIUM->value, 'label' => self::label(self::MEDIUM->value)],
            ['value' => self::HIGH->value, 'label' => self::label(self::HIGH->value)],
        ];
    }
}
