<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'test 1',
                'email' => 'test1@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],

            [
                'name' => 'test 2',
                'email' => 'test2@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],
            [
                'name' => 'test 3',
                'email' => 'test3@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],
            [
                'name' => 'test 4',
                'email' => 'test4@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],

            [
                'name' => 'test 5',
                'email' => 'test5@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],
            [
                'name' => 'test 6',
                'email' => 'test6@gmail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
            ],
        ]);
    }
}
