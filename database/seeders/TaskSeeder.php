<?php

namespace Database\Seeders;

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
            // User 1
            [
                'user_id' => 1,
                'title' => 'Viết Unit Test cho Auth & Task',
                'description' => 'Kiểm tra AuthService, TaskService và HTTP Requests với Pest PHP',
                'status' => 'doing',
                'due_date' => '2026-08-01',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Viết README và API Docs',
                'description' => 'Mô tả kiến trúc hệ thống và hướng dẫn cài đặt dự án',
                'status' => 'todo',
                'due_date' => '2026-08-10',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Khởi tạo dự án Laravel 13',
                'description' => 'Cài đặt môi trường, setup database và Laravel Sanctum',
                'status' => 'done',
                'due_date' => '2026-07-20',
                'completed_at' => '2026-07-19 15:30:00',
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Cấu hình Docker Environment',
                'description' => 'Tạo docker-compose file cho PHP 8.5, MySQL và Nginx',
                'status' => 'done',
                'due_date' => '2026-07-15',
                'completed_at' => '2026-07-18 10:00:00',
                'is_overdue' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User 2
            [
                'user_id' => 2,
                'title' => 'Dev Task Management API',
                'description' => 'Xây dựng TaskService, TaskController và các tính năng CRUD',
                'status' => 'doing',
                'due_date' => '2026-08-05',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'title' => 'Nghiên cứu OAuth2 & Socialite',
                'description' => 'Tích hợp đăng nhập bằng Google và GitHub cho ứng dụng',
                'status' => 'todo',
                'due_date' => '2026-08-15',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'title' => 'Dev Authentication API',
                'description' => 'AuthService, LoginRequest, RegisterRequest và Token handling',
                'status' => 'done',
                'due_date' => '2026-07-10',
                'completed_at' => '2026-07-12 09:15:00',
                'is_overdue' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User 3
            [
                'user_id' => 3,
                'title' => 'Tích hợp TailwindCSS v4',
                'description' => 'Setup styling, dark mode và responsive layout cho UI',
                'status' => 'doing',
                'due_date' => '2026-08-05',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'title' => 'Thiết kế giao diện Dashboard',
                'description' => 'Xây dựng mockup UI/UX trên Figma cho Todo list app',
                'status' => 'todo',
                'due_date' => '2026-08-12',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'title' => 'Thu thập yêu cầu người dùng',
                'description' => 'Tổng hợp các tính năng chính và luồng trải nghiệm người dùng',
                'status' => 'done',
                'due_date' => '2026-07-22',
                'completed_at' => '2026-07-21 17:00:00',
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User 4
            [
                'user_id' => 4,
                'title' => 'Tối ưu Database & Indexing',
                'description' => 'Thêm composite index cho [user_id, status] để tăng tốc truy vấn',
                'status' => 'doing',
                'due_date' => '2026-08-03',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'title' => 'Cấu hình Redis Cache',
                'description' => 'Cache dữ liệu danh sách công việc thường truy cập',
                'status' => 'todo',
                'due_date' => '2026-08-08',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'title' => 'Thiết kế Database Schema',
                'description' => 'Tạo ERD và migration cho bảng users, tasks và personal_access_tokens',
                'status' => 'done',
                'due_date' => '2026-07-22',
                'completed_at' => '2026-07-22 14:00:00',
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User 5
            [
                'user_id' => 5,
                'title' => 'Tạo Notification System',
                'description' => 'Gửi email nhắc nhở tự động khi công việc gần hoặc quá hạn',
                'status' => 'doing',
                'due_date' => '2026-08-03',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'title' => 'Cấu hình CI/CD Pipeline',
                'description' => 'Setup GitHub Actions tự động chạy Pint lint và Pest tests',
                'status' => 'todo',
                'due_date' => '2026-08-20',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'title' => 'Setup Logging & Monitoring',
                'description' => 'Tích hợp Laravel Pail và hệ thống log theo dõi lỗi',
                'status' => 'done',
                'due_date' => '2026-07-20',
                'completed_at' => '2026-07-20 11:30:00',
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // User 6
            [
                'user_id' => 6,
                'title' => 'Viết Integration Tests',
                'description' => 'Kiểm thử toàn bộ luồng từ Authentication đến CRUD Task',
                'status' => 'doing',
                'due_date' => '2026-08-03',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6,
                'title' => 'Đánh giá An ninh & Security Audit',
                'description' => 'Kiểm tra bảo mật API, SQL Injection, XSS và CORS headers',
                'status' => 'todo',
                'due_date' => '2026-08-18',
                'completed_at' => null,
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6,
                'title' => 'Refactor Task Architecture',
                'description' => 'Tách controller logic sang Service Layer theo chuẩn Clean Code',
                'status' => 'done',
                'due_date' => '2026-07-21',
                'completed_at' => '2026-07-21 16:45:00',
                'is_overdue' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
