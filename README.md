# 📝 TODO APP API & FRONTEND

## Hệ thống Quản lý Công việc (TODO App) xây dựng bằng **Laravel 11 (PHP 8.5)** cho Backend và **Vue 3 (Vite, TailwindCSS, TypeScript)** cho Frontend.

## 🛠️ Kiến Trúc Hệ Thống

Dự án áp dụng mô hình kiến trúc phân lớp:

$$\text{Controller} \longrightarrow \text{Service Layer} \longrightarrow \text{Eloquent Model}$$

### 📌 Lý Do Lựa Chọn Mô Hình:

- **Controller gọn nhẹ:** Controller chỉ đảm nhận vai trò tiếp nhận HTTP Request, xử lý validation (thông qua Form Request) và trả về HTTP Response.
- **Tách biệt nghiệp vụ (Business Logic):** Toàn bộ logic xử lý đăng nhập, đăng ký, lọc task, khôi phục task soft-delete... đều nằm tập trung tại `Service Layer`.
- **Dễ mở rộng & Kiểm thử:** Dễ dàng viết Unit Test / Feature Test cho từng hàm trong Service mà không phụ thuộc vào HTTP Context.
- **Tối ưu Eloquent:** Với ứng dụng CRUD tiêu chuẩn, Eloquent ORM của Laravel đã đủ mạnh mẽ nên không cần thêm lớp Repository rườm rà.

---

## 📡 Danh Sách API & Luồng Xử Lý

### 1. Xác thực người dùng (Authentication)

| Method | Endpoint             | Mô tả                            | Request Class     | Controller @ Method       |
| :----- | :------------------- | :------------------------------- | :---------------- | :------------------------ |
| `POST` | `/api/auth/register` | Đăng ký tài khoản mới            | `RegisterRequest` | `AuthController@register` |
| `POST` | `/api/auth/login`    | Đăng nhập hệ thống               | `LoginRequest`    | `AuthController@login`    |
| `POST` | `/api/auth/logout`   | Đăng xuất (Thu hồi Token)        | -                 | `AuthController@logout`   |
| `GET`  | `/api/me`            | Lấy thông tin tài khoản hiện tại | -                 | `AuthController@show`     |

#### 🔄 Luồng Đăng ký (`POST /api/auth/register`):

`POST /api/auth/register` $\rightarrow$ `RegisterRequest` (Validate input) $\rightarrow$ `AuthController@register` $\rightarrow$ `AuthService@register(data)` $\rightarrow$ `User::create(...)` $\rightarrow$ Trả về JSON Token (Status `201 Created`).

#### 🔄 Luồng Đăng nhập (`POST /api/auth/login`):

`POST /api/auth/login` $\rightarrow$ `LoginRequest` (Validate input) $\rightarrow$ `AuthController@login` $\rightarrow$ `AuthService@login(data)` $\rightarrow$ `Hash::check(...)` $\rightarrow$ Trả về JSON Token (Status `200 OK`).

---

### 2. Quản lý Công việc (Task Management)

| Method   | Endpoint                   | Mô tả                                   | Authorization |
| :------- | :------------------------- | :-------------------------------------- | :------------ |
| `GET`    | `/api/tasks`               | Lấy danh sách công việc                 | Bearer Token  |
| `GET`    | `/api/tasks/trashed`       | Lấy danh sách công việc trong thùng rác | Bearer Token  |
| `GET`    | `/api/tasks/{id}`          | Lấy chi tiết 1 công việc                | Bearer Token  |
| `POST`   | `/api/tasks`               | Tạo công việc mới                       | Bearer Token  |
| `PUT`    | `/api/tasks/{id}`          | Cập nhật thông tin công việc            | Bearer Token  |
| `PUT`    | `/api/tasks/{id}/complete` | Đánh dấu hoàn thành công việc           | Bearer Token  |
| `PUT`    | `/api/tasks/{id}/restore`  | Khôi phục công việc từ thùng rác        | Bearer Token  |
| `DELETE` | `/api/tasks/{id}`          | Xóa tạm thời (Soft Delete)              | Bearer Token  |
| `DELETE` | `/api/tasks/{id}/force`    | Xóa vĩnh viễn (Force Delete)            | Bearer Token  |

---

## 🧪 Hướng Dẫn Test API Bằng Postman

Trong thư mục gốc của dự án đã chuẩn bị sẵn **Postman Collection** và **Postman Environment**:

- `todo-app.postman_collection.json`
- `todo-app.postman_environment.json`

### 📥 Các bước Import & Sử dụng:

1. **Import File vào Postman:**
    - Mở Postman $\rightarrow$ Chọn nút **Import** ở góc trên bên trái.
    - Chọn cả 2 file `todo-app.postman_collection.json` và `todo-app.postman_environment.json` để nạp vào Postman.

2. **Chọn Environment:**
    - Ở góc trên bên phải của Postman, chuyển dropdown Environment từ _No Environment_ sang **`todo-app`**.

3. **Tự Động Hóa Quản Lý Token & Task ID:**
    - **Tự động lưu Token (`token`):** Khi gửi request **Register** hoặc **Login**, script của Postman sẽ tự động trích xuất `token` từ Response và lưu vào môi trường. Tất cả các Request sau đó (`GET /api/tasks`, `POST /api/tasks`...) sẽ tự động đính kèm Token trong Header `Authorization: Bearer {{token}}`.
    - **Tự động lưu ID công việc (`id`):** Khi gửi request **POST /api/tasks** (Tạo task mới), script sẽ tự động gán ID của công việc vừa tạo vào biến `{{id}}`. Các API cập nhật/xóa sau đó (`PUT /api/tasks/{{id}}`, `DELETE /api/tasks/{{id}}`) sẽ tự động sử dụng ID này.

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án (Docker)

### 📋 Yêu cầu hệ thống:

- Git
- Docker & Docker Compose

### 1. Clone repository

```bash
git clone https://github.com/thanhdt05/todo-app.git
cd todo-app
```

### 2. Cấu hình môi trường (`.env`)

```bash
cp .env.example .env
```

### 3. Khởi chạy Docker Containers

```bash
docker compose up -d --build
```

### 4. Cài đặt các gói phụ thuộc (Dependencies)

```bash
docker compose exec todoapp composer install
```

### 5. Khởi tạo App Key & Database

```bash
# Generate ứng dụng key
docker compose exec todoapp php artisan key:generate

# Chạy Migration tạo bảng
docker compose exec todoapp php artisan migrate

# Seed dữ liệu mẫu (nếu cần)
docker compose exec todoapp php artisan db:seed
```

### 6. Chạy Test Suites

```bash
docker compose exec todoapp php artisan test
```

Truy cập ứng dụng Web tại: **`http://localhost:5173`**
