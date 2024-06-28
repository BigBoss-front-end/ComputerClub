<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
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
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $errors
            ], 422); // 422 - Unprocessable Entity
        }

        // Проверяем, заблокирован ли пользователь
        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_blocked) {
            // Если пользователь заблокирован, возвращаем ошибку
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => [
                    'access' => ['Вы заблокированы']
                ]
            ], 422); // 422 - Unprocessable Entity
        }
        

        if (Auth::attempt($request->all(), true) && !Auth::user()->is_blocked) {
            return ['status' => 'success'];
        } else {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => [
                    'access' => ['Неверный логин или пароль']
                ]
            ], 422); // 422 - Unprocessable Entity
        }
    }

    public function logout()
    {
        //
    }

    public function index()
    {
        //
    }

    public function session()
    {
        return [
            'user' => User::query()->with(['role'])->findOrFail(Auth::id())
        ];
    }

    public function show($id)
    {
        return [
            'user' => User::query()->with(['role'])->findOrFail($id)
        ];
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id'
        ];

        // Выполняем валидацию
        $validator = Validator::make($request->all(), $rules);

        // Проверяем, прошла ли валидация
        if ($validator->fails()) {
            // Если есть ошибки валидации, получаем их из объекта запроса
            $errors = $validator->errors();

            // Делаем что-то с ошибками, например, возвращаем их как ответ
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $errors
            ], 422); // 422 - Unprocessable Entity
        }


        $hashedPassword = Hash::make($request->password);

        $request->merge([
            'password' => $hashedPassword
        ]);

        return response()->json([
            'message' => 'Пользователь создан',
            'user' => User::query()->create($request->all())->fresh(['role'])
        ]);
    }

    public function update($id, Request $request)
    {
        $request->merge([
            'id' => $id,
            'is_blocked' => $request->has('is_blocked') && $request->is_blocked != 0 ? 1 : 0,
        ]);

        $rules = [
            'id' => 'required|integer|exists:users,id',
            'name' => 'nullable|string',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => 'nullable|string|min:8',
            'role_id' => 'nullable|integer|exists:roles,id'
        ];

        // Выполняем валидацию
        $validator = Validator::make($request->all(), $rules);

        // Проверяем, прошла ли валидация
        if ($validator->fails()) {
            // Если есть ошибки валидации, получаем их из объекта запроса
            $errors = $validator->errors();

            // Делаем что-то с ошибками, например, возвращаем их как ответ
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $errors
            ], 422); // 422 - Unprocessable Entity
        }

        

        if ($request->filled('password')) {
            $hashedPassword = Hash::make($request->password);

            $request->merge([
                'password' => $hashedPassword
            ]);

            $data = $request->all();
        } else {
            $data = $request->except('password');
        }

        User::query()->where('id', $id)->update($data);

        return response()->json([
            'user' => User::query()->with('role')->findOrFail($id),
        ]);
    }

    public function destroy($id)
    {
        Validator::make([
            'id' => $id,
        ], [
            'id' => 'required|integer|exists:users,id',
        ]);

        User::query()->where('id', $id)->delete();

        return response()->json([
            'message' => 'Пользователь удален',
        ]);
    }
}
