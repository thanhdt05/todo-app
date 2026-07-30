<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HttpResponse;

    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return $this->success([
            'user' => UserResource::make($data['user']),
            'token' => $data['token'],
        ], 'Đăng ký thành công', 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return $this->success([
            'user' => UserResource::make($data['user']),
            'token' => $data['token'],
        ], 'Đăng nhập thành công');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->success([], 'Đăng xuất thành công');
    }

    public function show(Request $request)
    {
        return $this->success(
            UserResource::make($request->user()),
            'Lấy thông tin người dùng thành công'
        );
    }
}
