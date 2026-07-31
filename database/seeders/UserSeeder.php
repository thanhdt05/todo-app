<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'test 1',
                'email' => 'test1@gmail.com',
                'password' => '12345678',
                'role' => RoleName::ADMIN,
            ],
            [
                'name' => 'test 2',
                'email' => 'test2@gmail.com',
                'password' => '12345678',
                'role' => RoleName::ADMIN,
            ],
            [
                'name' => 'test 3',
                'email' => 'test3@gmail.com',
                'password' => '12345678',
                'role' => RoleName::USER,
            ],
            [
                'name' => 'test 4',
                'email' => 'test4@gmail.com',
                'password' => '12345678',
                'role' => RoleName::USER,
            ],
            [
                'name' => 'test 5',
                'email' => 'test5@gmail.com',
                'password' => '12345678',
                'role' => RoleName::USER,
            ],
            [
                'name' => 'test 6',
                'email' => 'test6@gmail.com',
                'password' => '12345678',
                'role' => RoleName::USER,
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );

            $user->syncRoles($role);
        }
    }
}
