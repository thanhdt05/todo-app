# TODO APP API & FRONTEND

Ứng dụng quản lý công việc full-stack sử dụng **Laravel 13 REST API**, **Vue 3**, **TypeScript**, **PostgreSQL**, **Redis** và **Laravel Horizon**.

Dự án hỗ trợ xác thực bằng email/mật khẩu, đăng nhập Google/Microsoft, quản lý vòng đời công việc, phân quyền RBAC (Spatie), tìm kiếm, lọc, phân trang phía server, thùng rác Soft Delete, cùng **hệ thống tự động thông báo nhắc nhở công việc sắp đến hạn qua Queue & Email**.

---

## Tính năng chính

### Xác thực và tài khoản

- Đăng ký tài khoản bằng email và mật khẩu (tự động gán role `user` mặc định trong DB transaction).
- Đăng nhập API bằng Laravel Sanctum Bearer Token.
- Đăng xuất và thu hồi token đang sử dụng.
- Xem thông tin tài khoản hiện tại kèm danh sách `roles` và `permissions`.
- Đăng nhập bằng Google hoặc Microsoft thông qua Laravel Socialite.
- Liên kết tài khoản mạng xã hội theo email đã tồn tại trong bảng `users`.
- Sử dụng mã trao đổi một lần (`exchange_code`) thay vì đưa Sanctum token trực tiếp lên URL callback.
- Trang thông tin cá nhân trên Vue frontend.
- Vue Router Guard bảo vệ các trang yêu cầu đăng nhập.
- Axios Interceptor tự động đính kèm Bearer Token và xử lý lỗi `401`.

### Quản lý công việc

- Tạo, xem, cập nhật và xóa công việc.
- Ba trạng thái công việc:
  - `todo`: Chưa làm.
  - `doing`: Đang làm.
  - `done`: Hoàn thành.
- Ba mức độ ưu tiên: `low`, `medium`, `high`.
- Lưu ngày hết hạn (`due_date`), thời điểm hoàn thành (`completed_at`), và thời điểm phát thông báo (`reminder_sent_at`).
- Tự động xác định công việc quá hạn qua trường `is_overdue`.
- Đánh dấu hoàn thành bằng endpoint riêng.
- Tìm kiếm theo tiêu đề hoặc mô tả.
- Lọc theo trạng thái và mức độ ưu tiên.
- Phân trang phía server, hỗ trợ thay đổi `per_page` từ 1 đến 100.
- Soft Delete và danh sách thùng rác.
- Khôi phục một công việc hoặc hàng loạt công việc.
- Xóa vĩnh viễn công việc trong thùng rác.

### Hàng đợi Queue, Thông báo & Horizon

- **Tự động nhắc nhở (Task Reminders)**: Laravel Scheduler chạy hàng giờ quét các Task chưa hoàn thành có `due_date` trong vòng 24 giờ tới để đẩy Job gửi thông báo.
- **Async Queue Processing**: Đẩy `SendTaskReminder` Job vào Redis Queue xử lý bất đồng bộ, dùng khóa Redis (`WithoutOverlapping`) tránh gửi trùng.
- **Thông báo đa kênh (Notifications)**:
  - **Database Notification**: Lưu thông báo vào bảng `notifications` để hiển thị trên giao diện hoặc qua API `GET /api/notifications`.
  - **Email Notification**: Gửi Email HTML thông báo tới người dùng với link liên kết trực tiếp tới ứng dụng.
- **Laravel Horizon Dashboard**: Giao diện quản lý & giám sát Queue realtime tại `http://localhost:8000/horizon`.
- **Thử nghiệm Email với Mailpit**: Tích hợp hòm thư Mailpit Web Inbox tại `http://localhost:8025` để xem giao diện Email HTML thử nghiệm trực quan.

### Phân quyền và bảo mật

- Người dùng thông thường chỉ truy cập các công việc thuộc tài khoản của mình.
- Quản trị viên có thể truy cập toàn bộ công việc.
- Task Policy kiểm soát quyền xem, tạo, cập nhật, hoàn thành, xóa, khôi phục và xóa vĩnh viễn.
- Form Request đảm nhiệm validation cho đăng ký, đăng nhập, danh sách, tạo, cập nhật và khôi phục hàng loạt.
- API Resource kiểm soát các trường được phép trả về frontend.
- `password` và `remember_token` được ẩn khỏi JSON của User model.
- OAuth `access_token` và `refresh_token` được mã hóa khi lưu trong cơ sở dữ liệu.
- Mã đăng nhập Social OAuth chỉ dùng một lần và hết hạn sau 60 giây.
- Auth API được giới hạn 10 request/phút.
- Các lỗi `401`, `403`, `404`, `422` và `500` được chuẩn hóa về JSON thống nhất.

---

## Công nghệ sử dụng

| Thành phần            | Công nghệ                                       |
| --------------------- | ----------------------------------------------- |
| Backend               | PHP 8.4, Laravel 13                             |
| API Authentication    | Laravel Sanctum                                 |
| Social Authentication | Laravel Socialite, SocialiteProviders Microsoft |
| Authorization         | Spatie Laravel Permission (RBAC)                |
| Queue & Caching       | Redis 7, Laravel Horizon                        |
| Mail Testing          | Mailpit                                         |
| Database              | PostgreSQL 16                                   |
| Frontend              | Vue 3, TypeScript, Vue Router                   |
| HTTP Client           | Axios                                           |
| UI                    | Tailwind CSS 4                                  |
| Build Tool            | Vite 8                                          |
| Backend Testing       | PHPUnit, Pest PHP                               |
| Code Formatting       | Laravel Pint                                    |
| Container             | Docker, Docker Compose                          |

> Docker image hiện sử dụng PHP 8.4 và Node.js 22. Frontend yêu cầu Node.js `^22.18.0` hoặc `>=24.12.0`.

---

## Kiến trúc xử lý

Dự án sử dụng kiến trúc phân lớp:

```text
HTTP Request
    ↓
Form Request Validation
    ↓
Controller + Policy
    ↓
Service Layer
    ↓
Eloquent Model
    ↓
API Resource
    ↓
HttpResponse JSON
```

### Vai trò của từng lớp

- **Form Request**: kiểm tra và chuẩn hóa dữ liệu đầu vào.
- **Controller**: tiếp nhận request, gọi authorization, chuyển dữ liệu cho Service và trả response.
- **Policy**: kiểm tra quyền thao tác trên từng công việc.
- **Service**: xử lý nghiệp vụ đăng nhập, task, soft delete, bulk restore và Social OAuth.
- **Model**: ánh xạ dữ liệu và quan hệ Eloquent.
- **API Resource**: xác định rõ các trường được trả về client.
- **HttpResponse**: chuẩn hóa response thành công, lỗi và phân trang.

---

## Chuẩn response API

### Response thành công

```json
{
  "success": true,
  "message": "Thao tác thành công",
  "data": {}
}
```

### Response danh sách phân trang

```json
{
  "success": true,
  "message": "Lấy danh sách thành công",
  "data": [],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 5,
    "to": 5,
    "total": 5
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  }
}
```

### Response validation thất bại

```json
{
  "success": false,
  "message": "Dữ liệu đầu vào không hợp lệ",
  "errors": {
    "title": [
      "Tiêu đề không được để trống"
    ]
  },
  "data": null
}
```

### Dữ liệu Task được transform

Task API Resource hiện trả các trường:

```text
id
user
title
description
status
due_date
completed_at
is_overdue
created_at
updated_at
```

`UserResource` chỉ trả email khi tài nguyên người dùng đó là chính người đang đăng nhập. Các trường nhạy cảm như mật khẩu và token OAuth không được đưa vào task response.

---

## Danh sách API

Base URL mặc định:

```text
http://localhost:8000/api
```

Các API được bảo vệ cần header:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

### 1. Authentication API

| Method | Endpoint                    | Mô tả                                            | Xác thực     |
| ------ | --------------------------- | ------------------------------------------------ | ------------ |
| `POST` | `/api/auth/register`        | Đăng ký tài khoản                                | Không        |
| `POST` | `/api/auth/login`           | Đăng nhập bằng email/mật khẩu                    | Không        |
| `POST` | `/api/auth/social/exchange` | Đổi Social OAuth exchange code lấy Sanctum token | Không        |
| `POST` | `/api/logout`               | Thu hồi token hiện tại                           | Bearer Token |
| `GET`  | `/api/me`                   | Lấy thông tin người dùng hiện tại                | Bearer Token |

### 2. Social OAuth Web Routes

| Method | Endpoint                   | Mô tả                                           |
| ------ | -------------------------- | ----------------------------------------------- |
| `GET`  | `/auth/google`             | Chuyển người dùng đến trang đăng nhập Google    |
| `GET`  | `/auth/google/callback`    | Xử lý callback Google                           |
| `GET`  | `/auth/microsoft`          | Chuyển người dùng đến trang đăng nhập Microsoft |
| `GET`  | `/auth/microsoft/callback` | Xử lý callback Microsoft                        |

> Social login chỉ thành công khi email Google/Microsoft đã tồn tại trong bảng `users`. Chức năng hiện tại không tự tạo user mới từ tài khoản mạng xã hội.

#### Luồng Social OAuth

```text
Vue Login
    ↓
GET /auth/{provider}
    ↓
Google hoặc Microsoft OAuth
    ↓
GET /auth/{provider}/callback
    ↓
Kiểm tra user theo email và liên kết social account
    ↓
Tạo exchange_code dùng một lần, hiệu lực 60 giây
    ↓
Redirect đến FRONTEND_URL/auth/callback?exchange_code=...
    ↓
Vue gọi POST /api/auth/social/exchange
    ↓
Nhận Sanctum Bearer Token
```

---

### 3. Task API

| Method         | Endpoint                   | Mô tả                              |
| -------------- | -------------------------- | ---------------------------------- |
| `GET`          | `/api/tasks`               | Lấy danh sách task đang hoạt động  |
| `GET`          | `/api/tasks/trashed`       | Lấy danh sách task trong thùng rác |
| `GET`          | `/api/tasks/{id}`          | Lấy chi tiết task                  |
| `POST`         | `/api/tasks`               | Tạo task mới                       |
| `PUT`, `PATCH` | `/api/tasks/{id}`          | Cập nhật task                      |
| `PUT`          | `/api/tasks/{id}/complete` | Đánh dấu task hoàn thành           |
| `PUT`          | `/api/tasks/{id}/restore`  | Khôi phục một task                 |
| `PUT`          | `/api/tasks/bulk-restore`  | Khôi phục nhiều task               |
| `DELETE`       | `/api/tasks/{id}`          | Xóa tạm task                       |
| `DELETE`       | `/api/tasks/{id}/force`    | Xóa vĩnh viễn task                 |

Tất cả Task API đều yêu cầu Bearer Token.

#### Query lấy danh sách task

```http
GET /api/tasks?keyword=laravel&status=doing&page=1&per_page=5
```

| Query      | Kiểu    | Mô tả                                          |
| ---------- | ------- | ---------------------------------------------- |
| `keyword`  | string  | Tìm trong tiêu đề hoặc mô tả, tối đa 255 ký tự |
| `status`   | string  | `todo`, `doing` hoặc `done`                    |
| `page`     | integer | Trang hiện tại, tối thiểu 1                    |
| `per_page` | integer | Số item mỗi trang, từ 1 đến 100; mặc định 5    |

---

## Cài đặt và chạy bằng Docker

### Yêu cầu

- Git.
- Docker Engine hoặc Docker Desktop.
- Docker Compose.

### 1. Clone repository

```bash
git clone https://github.com/thanhdt05/todo-app.git
cd todo-app
```

### 2. Tạo file môi trường

```bash
cp .env.example .env
```

Đảm bảo các biến chính có giá trị phù hợp:

```env
APP_NAME="Todo App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=todo_app
DB_USERNAME=todo
DB_PASSWORD=secret
```

Frontend mặc định gọi API tại `http://localhost:8000/api`. Có thể tạo `frontend/.env` để thay đổi:

```env
VITE_API_URL=http://localhost:8000/api
```

### 3. Cấu hình Social OAuth nếu sử dụng

#### Microsoft

```env
MICROSOFT_CLIENT_ID=your-client-id
MICROSOFT_CLIENT_SECRET=your-client-secret
MICROSOFT_TENANT_ID=common
MICROSOFT_REDIRECT_URL=http://localhost:8000/auth/microsoft/callback
```

#### Google

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

Redirect URI đăng ký tại Google/Microsoft phải trùng chính xác với giá trị trong `.env`.

### 4. Build Docker images

```bash
docker compose build
```

### 5. Cài dependencies

```bash
# Backend

docker compose run --rm --no-deps todoapp composer install

# Frontend

docker compose run --rm --no-deps frontend npm install
```

### 6. Khởi chạy containers

```bash
docker compose up -d
```

### 7. Khởi tạo ứng dụng và database

```bash
docker compose exec todoapp php artisan key:generate
docker compose exec todoapp php artisan migrate:fresh --seed
```

### 8. Truy cập ứng dụng

```text
Frontend:        http://localhost:5173
Backend:         http://localhost:8000
API:             http://localhost:8000/api
Laravel Horizon: http://localhost:8000/horizon (Dashboard quản lý Queue)
Mailpit Web UI:  http://localhost:8025         (Hòm thư xem Email thử nghiệm)
Health:          http://localhost:8000/up
```

---

## Tài khoản seed

Sau khi chạy `php artisan migrate:fresh --seed`, có thể sử dụng các tài khoản mẫu:

| Email             | Mật khẩu   | Role  |
| ----------------- | ---------- | ----- |
| `test1@gmail.com` | `12345678` | Admin |
| `test2@gmail.com` | `12345678` | Admin |
| `test3@gmail.com` | `12345678` | User  |
| `test4@gmail.com` | `12345678` | User  |
| `test5@gmail.com` | `12345678` | User  |
| `test6@gmail.com` | `12345678` | User  |

---

## Test và kiểm tra chất lượng

### 1. Kiểm thử Email & Horizon Queue thủ công

- **Mailpit Web UI**: Truy cập `http://localhost:8025` để xem toàn bộ Email thông báo được gửi dưới dạng giao diện HTML trực quan.
- **Laravel Horizon**: Truy cập `http://localhost:8000/horizon` (tài khoản Admin) để theo dõi các Job được xử lý realtime trên Redis.
- **Chạy thử Command nhắc nhở thủ công**:
  ```bash
  docker compose exec todoapp php artisan tasks:dispatch-reminders
  ```

### 2. Backend tests (Pest PHP)

```bash
docker compose exec todoapp php artisan test
```

Chạy riêng test nhắc nhở Task Reminder:

```bash
docker compose exec todoapp php artisan test --filter=TaskReminderTest
```

Bộ test hiện có bao phủ các nhóm chính:

- Register, login, login sai mật khẩu và profile.
- Task CRUD và phân trang.
- Task trong thùng rác, restore và force delete.
- Task Reminders, Job dispatching và Notification assertion.
- Validation dữ liệu không hợp lệ.
- Ngăn người dùng truy cập hoặc chỉnh sửa task của người khác.
- Mock Social OAuth cho Google và Microsoft.


## Kiểm thử API bằng Postman

Repository có sẵn:

```text
todo-app.postman_collection.json
todo-app.postman_environment.json
```

Cách sử dụng:

1. Import cả hai file vào Postman.
2. Chọn environment `todo-app`.
3. Gửi request Register hoặc Login.
4. Script trong collection lưu token vào biến môi trường.
5. Các request protected sử dụng tự động:

```http
Authorization: Bearer {{token}}
```

Khi tạo task, collection có thể lưu ID task để sử dụng cho các request xem, cập nhật và xóa tiếp theo.
