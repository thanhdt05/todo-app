<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tasks')->insert([
            [
                "user_id" => 1,
                "title" => "Viết Unit Test",
                "description" => "Kiểm tra AuthService và TaskService",
                "status" => "doing",
                "due_date" => "2026-08-01 09:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 1,
                "title" => "Viết README",
                "description" => "Mô tả kiến trúc và hướng dẫn cài đặt",
                "status" => "todo",
                "due_date" => "2026-08-02 15:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 2,
                "title" => "Dev APIAuth",
                "description" => "AuthService, LoginRequest, RegisterRequest",
                "status" => "done",
                "due_date" => "2026-07-25 08:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 2,
                "title" => "Dev Task",
                "description" => "TaskService và các CRUD",
                "status" => "doing",        
                "due_date" => "2026-08-05 17:00:00",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "user_id" => 3,
                "title" => "Task 1",
                "description" => "Description 1",
                "status" => "todo",
                "due_date" => "2026-08-05 17:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 3,
                "title" => "Task 2",
                "description" => "Description 2",
                "status" => "todo",
                "due_date" => "2026-08-05 17:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 4,
                "title" => "Tối ưu Database",
                "description" => "Thêm composite index cho user_id và status",
                "status" => "doing",
                "due_date" => "2026-08-03 14:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 5,
                "title" => "Dev APIAuth",
                "description" => "AuthService, LoginRequest, RegisterRequest",
                "status" => "doing",
                "due_date" => "2026-08-03 14:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 5,
                "title" => "Task 5",
                "description" => "Description 5",
                "status" => "doing",
                "due_date" => "2026-08-03 14:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 6,
                "title" => "Task 6",
                "description" => "Description 6",
                "status" => "todo",
                "due_date" => "2026-08-03 14:00:00",
                "created_at" => now(),
                "updated_at" => now(),
            ]
        ]);
    }
}
