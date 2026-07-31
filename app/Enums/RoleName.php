<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản trị viên',
            self::MANAGER => 'Quản lý',
            self::USER => 'Người dùng',
        };
    }
}
