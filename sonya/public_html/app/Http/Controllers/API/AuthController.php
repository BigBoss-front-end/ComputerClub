<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required|string',
            ];

            // Выполняем валидацию
            $validator = Validator::make($request->all(), $rules);

            // Проверяем, прошла ли валидация
            if ($validator->fails()) {
                // Если есть ошибки валидации, получаем их из объекта запроса
                $errors = $validator->errors();

                // Делаем что-то с ошибками, например, возвращаем их как ответ
                return  ResponseService::error([
                    'message' => 'Ошибка валидации',
                    'errors' => $errors
                ], 422); // 422 - Unprocessable Entity
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                Auth::login($user, true);
                return ResponseService::success(['user' => $user]);
            }

            return ResponseService::error([
                'message' => 'Ошибка валидации',
                'errors' => [
                    'login' => ['Неверный логин или пароль'],
                ]
            ], 422);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return ResponseService::error(['message' => 'Успешный выход из системы']);
    }
}
