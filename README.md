TODO APP

## Kiến trúc đã chọn

## Mô hình

B: Controller → Service → Model

## Lý do

Controller chỉ nhận HTTP Request và gọi Service thực hiện nghiệp vụ
Toàn bộ nghiệp vu như đăng nhập, đăng ký và CURD của task đều được Service thực hiện giúp Controller ngắn gọn
Repository không quá cần thiết với các nghiệp vụ cơ bản chỉ cần dùng Eloquent

## Luồng

POST /api/auth/register
→ RegisterRequest (validate)
→ AuthController@register
→ AuthService@register(data)
→ User::create(...)
→ JSON 201

POST /api/auth/login
→ LoginRequest (validate)
→ AuthController@login
→ AuthService@login(data)
→ User::where('email', ...)->first()
→ Hash::check(...)
→ JSON 200

POST /api/auth/logout
→ AuthController@logout
→ AuthService@logout(user)
→ JSON 204

GET /api/auth/me
→ AuthController@show
→ JSON 200

GET /api/tasks
→ TaskController@index
→ TaskService@all(user)
→ User::tasks()->get()
→ JSON 200

GET /api/tasks/{id}
→ TaskController@show
→ TaskService@findById(user)
→ User::tasks()->find()
→ JSON 200

POST /api/tasks
→ StoreTaskRequest (validate)
→ TaskController@store
→ TaskService@create(user, data)
→ Task::create(...)
→ JSON 201

PUT /api/tasks/{id}
→ UpdateTaskRequest (validate)
→ TaskController@update
→ TaskService@update(user, id, data)
→ Task::where('id', $id)->first()->update(...)
→ JSON 200

PUT /api/tasks/{id}/restore
→ TaskController@restore
→ TaskService@restore(user, id)
→ Task::where('id', $id)->first()->restore()
→ JSON 200

DELETE /api/tasks/{id}
→ TaskController@delete
→ TaskService@delete(user, id)
→ Task::where('id', $id)->first()->delete()
→ JSON 200

DELETE /api/tasks/{id}/force
→ TaskController@destroy
→ TaskService@forceDelete(user, id)
→ Task::where('id', $id)->first()->forceDelete()
→ JSON 200
