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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (TaskPermission::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => self::GUARD,
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => RoleName::ADMIN->value,
            'guard_name' => self::GUARD,
        ]);

        $manager = Role::firstOrCreate([
            'name' => RoleName::MANAGER->value,
            'guard_name' => self::GUARD,
        ]);

        $user = Role::firstOrCreate([
            'name' => RoleName::USER->value,
            'guard_name' => self::GUARD,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin->syncPermissions(TaskPermission::values());

        $manager->syncPermissions(array_map(
            static fn (TaskPermission $permission): string => $permission->value,
            [
                TaskPermission::VIEW_ALL,
                TaskPermission::VIEW_OWN,
                TaskPermission::VIEW_TRASHED_ALL,
                TaskPermission::VIEW_TRASHED_OWN,
                TaskPermission::CREATE,
                TaskPermission::UPDATE_ALL,
                TaskPermission::UPDATE_OWN,
                TaskPermission::COMPLETE_ALL,
                TaskPermission::COMPLETE_OWN,
                TaskPermission::DELETE_OWN,
                TaskPermission::RESTORE_ALL,
                TaskPermission::RESTORE_OWN,
            ]
        ));

        $user->syncPermissions(array_map(
            static fn (TaskPermission $permission): string => $permission->value,
            [
                TaskPermission::VIEW_OWN,
                TaskPermission::VIEW_TRASHED_OWN,
                TaskPermission::CREATE,
                TaskPermission::UPDATE_OWN,
                TaskPermission::COMPLETE_OWN,
                TaskPermission::DELETE_OWN,
                TaskPermission::RESTORE_OWN,
            ]
        ));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
