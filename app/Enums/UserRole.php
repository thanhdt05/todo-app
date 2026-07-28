<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function label(string $role): string
    {
        return match ($role) {
            self::ADMIN->value => 'Quản trị viên',
            self::USER->value => 'Người dùng',
        };
    }

    public static function options(): array
    {
        return [
            ['value' => self::ADMIN->value, 'label' => self::label(self::ADMIN->value)],
            ['value' => self::USER->value, 'label' => self::label(self::USER->value)],
        ];
    }

    public static function canViewAllTasks(string $role): bool
    {
        return $role === self::ADMIN->value;
    }
}
