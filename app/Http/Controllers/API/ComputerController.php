<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use App\Models\Computer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComputerController extends Controller
{
    public function list(Request $request)
    {
        try {

            $rules = [
                'filter.name' => 'nullable|string',
                'filter.sort' => 'nullable|integer',
                'filter.status_id' => 'nullable|integer|exists:statuses,id',
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

            $model = Computer::query();

            if($request->filled('filter.name')) {
                $model->where('name', 'LIKE', '%'.$request->input('filter.name').'%');
            }

            if($request->filled('filter.sort')) {
                $model->where('sort', $request->input('filter.sort'));
            }

            if($request->filled('filter.status_id')) {
                $model->where('status_id', $request->input('filter.status_id'));
            }

            if($request->filled('filter.user_id')) {
                $model->where('user_id', $request->input('filter.user_id'));
            }

            if($request->filled('with')) {
                $model->with($request->input('with'));
            }

            $computers = $model->paginate(100, ['*'], 'page', $request->filled('filter.page') ? $request->input('filter.page') : 1);

            return ResponseService::success([
                'computers' => $computers
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function one(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer|exists:computers,id',
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

            $model = Computer::query();

            $model->with('bookings');

            $computer = $model->find($request->id);

            ResponseService::success([
                'computer' => $computer
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            // Заполняем недостающие данные
            $request->merge([
                'status'  => $request->empty('status') ? 'free' : $request->status,
                'user_id' => Auth::id(),
            ]);

            $rules = [
                'name' => 'required|string',
                'sort' => 'required|integer',
                'status_id' => 'nullable|integer|exists:statuses,id',
                'user_id' => 'nullable|integer|exists:users,id',
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

            $model = Computer::query();

            $computer = $model->create($validator->validated())->refresh();

            ResponseService::success([
                'computer' => $computer
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:computers,id',
                'name' => 'nullable|string',
                'sort' => 'nullable|integer',
                'status_id' => 'nullable|integer|exists:statuses,id',
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

            $model = Computer::query();

            $model->where('id', $data['id'])->update($data);

            $computer = $model->find($data['id']);

            ResponseService::success([
                'computer' => $computer
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:computers,id',
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

            $model = Computer::query();

            $model->where('id', $request->id)->delete();

            ResponseService::success();
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }
}