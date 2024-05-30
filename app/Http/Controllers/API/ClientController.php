<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function list(Request $request)
    {
        try {

            $rules = [
                'filter.name' => 'nullable|string',
                'filter.phone' => 'nullable|string',
                'filter.email' => 'nullable|string|email',
                'filter.user_id' => 'nullable|integer|exists:users,id',
                'filter.page' => 'nullable|integer',
                'with' => 'nullable|array',
                'with.*' => 'nullable|string|in:bookings',
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

            $model = Client::query();

            if($request->filled('filter.name')) {
                $model->where('name', 'LIKE', '%'.$request->input('filter.name').'%');
            }

            if($request->filled('filter.phone')) {
                $model->where('phone', 'LIKE', '%'.$request->input('filter.phone').'%');
            }

            if($request->filled('filter.email')) {
                $model->where('email', 'LIKE', '%'.$request->input('filter.email').'%');
            }

            if($request->filled('filter.user_id')) {
                $model->where('user_id', $request->input('filter.user_id'));
            }

            if($request->filled('with')) {
                $model->with($request->input('with'));
            }

            $clients = $model->paginate(100, ['*'], 'page', $request->filled('filter.page') ? $request->input('filter.page') : 1);

            return ResponseService::success([
                'clients' => $clients
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function one(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer|exists:clients,id',
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

            $model = Client::query();

            $model->with('bookings');

            $client = $model->find($request->id);

            return ResponseService::success([
                'client' => $client
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            // Заполняем недостающие данные
            $request->merge([
                'user_id' => Auth::id(),
            ]);

            $rules = [
                'name' => 'required|string',
                'phone' => 'required|string|unique:clients,phone',
                'email' => 'nullable|string|email|unique:clients,email',
                'user_id' => 'required|integer|exists:users,id',
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

            $model = Client::query();

            $client = $model->create($validator->validated())->refresh();

            return ResponseService::success([
                'client' => $client,
                'message' => 'Клиент создан'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:clients,id',
                'phone' => [
                    'required',
                    'string',
                    Rule::unique('clients', 'phone')->ignore($request->id),
                ],
                'email' => [
                    'nullable',
                    'string',
                    'email',
                    Rule::unique('clients', 'email')->ignore($request->id),
                ],
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

            $data = $validator->validated();

            $model = Client::query();

            $model->where('id', $data['id'])->update($data);

            $client = $model->find($data['id']);

            return ResponseService::success([
                'client' => $client,
                'message' => 'Клиент обновлён'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:clients,id',
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

            $model = Client::query();

            $model->where('id', $request->id)->delete();

            return ResponseService::success([
                'message' => 'Клиент удалён'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }
}