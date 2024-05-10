<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use App\Models\Client;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    public function list(Request $request)
    {
        try {

            $rules = [
                'filter.name' => 'nullable|string',
                'filter.page' => 'nullable|integer',
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

            $model = Status::query();

            if($request->filled('filter.name')) {
                $model->where('name', 'LIKE', '%'.$request->input('filter.name').'%');
            }

            $status = $model->paginate(100, ['*'], 'page', $request->filled('filter.page') ? $request->input('filter.page') : 1);

            return ResponseService::success([
                'status' => $status
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function one(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer|exists:statuses,id',
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

            $status = $model->find($request->id);

            ResponseService::success([
                'status' => $status
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }
}