# TODO APP API & FRONTEND

## 🛠️ Kiến Trúc Hệ Thống

Dự án áp dụng mô hình kiến trúc phân lớp:

$$\text{Controller} \longrightarrow \text{Service Layer} \longrightarrow \text{Model}$$

### Lý Do Lựa Chọn Mô Hình:

- Controller chỉ đảm nhận vai trò tiếp nhận HTTP Request, xử lý validation (thông qua Form Request) và trả về HTTP Response.
- Toàn bộ logic xử lý đăng nhập, đăng ký, lọc task, khôi phục task soft-delete... đều nằm tập trung tại lớp Service.
- Với ứng dụng CRUD tiêu chuẩn, Eloquent ORM của Laravel đã đủ mạnh nên không cần thêm lớp Repository.

## Danh Sách API & Luồng Xử Lý

### 1. Xác thực người dùng

| Method | Endpoint             | Mô tả                            | Request Class     | Controller @ Method       |
| :----- | :------------------- | :------------------------------- | :---------------- | :------------------------ |
| `POST` | `/api/auth/register` | Đăng ký tài khoản mới            | `RegisterRequest` | `AuthController@register` |
| `POST` | `/api/auth/login`    | Đăng nhập hệ thống               | `LoginRequest`    | `AuthController@login`    |
| `POST` | `/api/auth/logout`   | Đăng xuất (Thu hồi Token)        | -                 | `AuthController@logout`   |
| `GET`  | `/api/me`            | Lấy thông tin tài khoản hiện tại | -                 | `AuthController@show`     |

#### Luồng Đăng ký (`POST /api/auth/register`):

`POST /api/auth/register` $\rightarrow$ `RegisterRequest` (validate) $\rightarrow$ `AuthController@register` $\rightarrow$ `AuthService@register(data)` $\rightarrow$ `User::create(...)` $\rightarrow$ JSON 201

#### Luồng Đăng nhập (`POST /api/auth/login`):

`POST /api/auth/login` $\rightarrow$ `LoginRequest` (validate) $\rightarrow$ `AuthController@login` $\rightarrow$ `AuthService@login(data)` $\rightarrow$ `User::where('email', ...)->first()` $\rightarrow$ `Hash::check(...)` $\rightarrow$ JSON 200

#### Luồng Đăng xuất (`POST /api/auth/logout`):

`POST /api/auth/logout` $\rightarrow$ `AuthController@logout` $\rightarrow$ `AuthService@logout(user)` $\rightarrow$ JSON 204

#### Luồng Lấy thông tin cá nhân (`GET /api/me`):

`GET /api/me` $\rightarrow$ `AuthController@show` $\rightarrow$ JSON 200

### 2. Quản lý Công việc

| Method   | Endpoint                   | Mô tả                             | Authorization |
| :------- | :------------------------- | :-------------------------------- | :------------ |
| `GET`    | `/api/tasks`               | Lấy danh sách công việc           | Bearer Token  |
| `GET`    | `/api/tasks/trashed`       | Lấy danh sách công việc đã bị xóa | Bearer Token  |
| `GET`    | `/api/tasks/{id}`          | Lấy chi tiết 1 công việc          | Bearer Token  |
| `POST`   | `/api/tasks`               | Tạo công việc mới                 | Bearer Token  |
| `PUT`    | `/api/tasks/{id}`          | Cập nhật thông tin công việc      | Bearer Token  |
| `PUT`    | `/api/tasks/{id}/complete` | Đánh dấu hoàn thành công việc     | Bearer Token  |
| `PUT`    | `/api/tasks/{id}/restore`  | Khôi phục công việc từ thùng rác  | Bearer Token  |
| `DELETE` | `/api/tasks/{id}`          | Xóa tạm thời (Soft Delete)        | Bearer Token  |
| `DELETE` | `/api/tasks/{id}/force`    | Xóa vĩnh viễn (Force Delete)      | Bearer Token  |

#### Luồng Lấy danh sách công việc (`GET /api/tasks`):

`GET /api/tasks` $\rightarrow$ `TaskController@index` $\rightarrow$ `TaskService@all(user)` $\rightarrow$ `User::tasks()->get()` $\rightarrow$ JSON 200

#### Luồng Lấy danh sách công việc đã xóa (`GET /api/tasks/trashed`):

`GET /api/tasks/trashed` $\rightarrow$ `TaskController@trashed` $\rightarrow$ `TaskService@trashed(user)` $\rightarrow$ `User::tasks()->onlyTrashed()->get()` $\rightarrow$ JSON 200

#### Luồng Lấy chi tiết công việc (`GET /api/tasks/{id}`):

`GET /api/tasks/{id}` $\rightarrow$ `TaskController@show` $\rightarrow$ `TaskService@findById(user)` $\rightarrow$ `User::tasks()->find()` $\rightarrow$ JSON 200

#### Luồng Tạo công việc mới (`POST /api/tasks`):

`POST /api/tasks` $\rightarrow$ `StoreTaskRequest` (validate) $\rightarrow$ `TaskController@store` $\rightarrow$ `TaskService@create(user, data)` $\rightarrow$ `Task::create(...)` $\rightarrow$ JSON 201

#### Luồng Cập nhật công việc (`PUT /api/tasks/{id}`):

`PUT /api/tasks/{id}` $\rightarrow$ `UpdateTaskRequest` (validate) $\rightarrow$ `TaskController@update` $\rightarrow$ `TaskService@update(user, id, data)` $\rightarrow$ `Task::where('id', $id)->first()->update(...)` $\rightarrow$ JSON 200

#### Luồng Đánh dấu hoàn thành (`PUT /api/tasks/{id}/complete`):

`PUT /api/tasks/{id}/complete` $\rightarrow$ `TaskController@complete` $\rightarrow$ `TaskService@complete(user, id)` $\rightarrow$ `Task::where('id', $id)->first()->update(...)` $\rightarrow$ JSON 200

#### Luồng Khôi phục công việc (`PUT /api/tasks/{id}/restore`):

`PUT /api/tasks/{id}/restore` $\rightarrow$ `TaskController@restore` $\rightarrow$ `TaskService@restore(user, id)` $\rightarrow$ `Task::where('id', $id)->first()->restore()` $\rightarrow$ JSON 200

#### Luồng Xóa tạm thời (`DELETE /api/tasks/{id}`):

`DELETE /api/tasks/{id}` $\rightarrow$ `TaskController@delete` $\rightarrow$ `TaskService@delete(user, id)` $\rightarrow$ `Task::where('id', $id)->first()->delete()` $\rightarrow$ JSON 200

#### Luồng Xóa vĩnh viễn (`DELETE /api/tasks/{id}/force`):

`DELETE /api/tasks/{id}/force` $\rightarrow$ `TaskController@destroy` $\rightarrow$ `TaskService@forceDelete(user, id)` $\rightarrow$ `Task::where('id', $id)->first()->forceDelete()` $\rightarrow$ JSON 200

## Hướng Dẫn Test API Bằng Postman

Trong thư mục gốc của dự án đã chuẩn bị sẵn **Postman Collection** và **Postman Environment**:

- `todo-app.postman_collection.json`
- `todo-app.postman_environment.json`

### Các bước Import & Sử dụng:

1. **Import File vào Postman:**
    - Mở Postman $\rightarrow$ Chọn nút **Import** ở góc trên bên trái.
    - Chọn cả 2 file `todo-app.postman_collection.json` và `todo-app.postman_environment.json` để nạp vào Postman.

2. **Chọn Environment:**
    - Ở góc trên bên phải của Postman, chuyển dropdown Environment từ _No Environment_ sang **`todo-app`**.

3. **Tự Động Hóa Quản Lý Token & Task ID:**
    - **Tự động lưu Token:** Khi gửi request **Register** hoặc **Login**, script của Postman sẽ tự động trích xuất `token` từ Response và lưu vào môi trường. Tất cả các Request sau đó (`GET /api/tasks`, `POST /api/tasks`...) sẽ tự động đính kèm Token trong Header `Authorization: Bearer {{token}}`.
    - **Tự động lưu ID công việc:** Khi gửi request **POST /api/tasks** (Tạo task mới), script sẽ tự động gán ID của công việc vừa tạo vào biến `{{id}}`. Các API cập nhật/xóa sau đó (`PUT /api/tasks/{{id}}`, `DELETE /api/tasks/{{id}}`) sẽ tự động sử dụng ID này.

## Hướng Dẫn Cài Đặt & Chạy Dự Án (Docker)

### Yêu cầu hệ thống:

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

#### ⚠️ Lưu Ý Về Môi Trường Test (`.env.testing`):

Khi chạy `php artisan test`, các file test sử dụng trait `RefreshDatabase` sẽ tự động thực thi `php artisan migrate:fresh` để reset lại Database. Để bảo vệ dữ liệu MySQL chính không bị xóa sạch, hãy khởi tạo file **`.env.testing`** tại thư mục gốc:

##### Cấu hình file `.env.testing`:

```env
APP_ENV=testing
APP_KEY=base64:c29tZXJhbmRvbXN0cmluZ2ZvcmpzZWN1cml0eWtleTEyMw==

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

##### Cơ chế hoạt động:

1. Khi chạy `php artisan test`, Laravel tự động nạp cấu hình từ file `.env.testing`.
2. Khởi tạo Database SQLite tạm thời trên RAM (`:memory:`).
3. Mọi thao tác `migrate:fresh` chỉ diễn ra trên RAM và tự hủy khi test hoàn thành.
4. Database MySQL chính được bảo vệ an toàn 100%.

##### Lệnh chạy test:

```bash
docker compose exec todoapp php artisan test
```

Truy cập ứng dụng Web tại: **`http://localhost:5173`**
