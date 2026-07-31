<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\TaskPermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private const string GUARD = 'web';

    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        foreach (TaskPermission::cases() as $permission) {
            Permission::findOrCreate(
                $permission->value,
                self::GUARD
            );
        }

        $registrar->forgetCachedPermissions();

        $admin = Role::findOrCreate(
            RoleName::ADMIN->value,
            self::GUARD
        );

        $manager = Role::findOrCreate(
            RoleName::MANAGER->value,
            self::GUARD
        );

        $user = Role::findOrCreate(
            RoleName::USER->value,
            self::GUARD
        );

        $admin->syncPermissions(TaskPermission::values());

        $manager->syncPermissions([
            TaskPermission::VIEW_ALL->value,
            TaskPermission::VIEW_TRASHED_ALL->value,
            TaskPermission::CREATE->value,
            TaskPermission::UPDATE_ALL->value,
            TaskPermission::COMPLETE_ALL->value,
            TaskPermission::DELETE_ALL->value,
            TaskPermission::RESTORE_ALL->value,
        ]);

        $user->syncPermissions([
            TaskPermission::VIEW_OWN->value,
            TaskPermission::VIEW_TRASHED_OWN->value,
            TaskPermission::CREATE->value,
            TaskPermission::UPDATE_OWN->value,
            TaskPermission::COMPLETE_OWN->value,
            TaskPermission::DELETE_OWN->value,
            TaskPermission::RESTORE_OWN->value,
        ]);

        $registrar->forgetCachedPermissions();
    }
}

