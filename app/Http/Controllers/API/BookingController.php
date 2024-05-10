<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function list(Request $request)
    {
        try {

            $rules = [
                'filter.computer_id' => 'nullable|integer|exists:computers,id',
                'filter.status_id' => 'nullable|integer|exists:statuses,id',
                'filter.client_id' => 'nullable|integer|exists:clients,id',
                'filter.page' => 'nullable|integer',
                'filter.start_time' => 'nullable|date',
                'filter.end_time' => 'nullable|date',
                'with' => 'nullable|array',
                'with.*' => 'nullable|string|in:computer,client',
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

            $model = Booking::query();

            if($request->filled('filter.computer_id')) {
                $model->where('computer_id', $request->input('filter.computer_id'));
            }

            if($request->filled('filter.status_id')) {
                $model->where('status_id', $request->input('filter.status_id'));
            }

            if($request->filled('filter.client_id')) {
                $model->where('client_id', $request->input('filter.client_id'));
            }

            if($request->filled('filter.start_time')) {
                $model->where('start_time', '>=', $request->input('filter.start_time'));
            }

            if($request->filled('filter.end_time')) {
                $model->where('end_time', '<=', $request->input('filter.end_time'));
            }

            if($request->filled('with')) {
                $model->with($request->input('with'));
            }

            $bookings = $model->paginate(100, ['*'], 'page', $request->filled('filter.page') ? $request->input('filter.page') : 1);

            return ResponseService::success([
                'bookings' => $bookings
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function one(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer|exists:bookings,id',
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

            $model = Booking::query();

            $model->with(['client', 'computer']);

            $booking = $model->find($request->id);

            ResponseService::success([
                'booking' => $booking
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $rules = [
                'computer_id' => 'required|integer|exists:computers,id',
                'status_id' => 'required|integer|exists:statuses,id',
                'client_id' => 'required|integer|exists:clients,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
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

            $model = Booking::query();

            $booking = $model->create($validator->validated())->refresh();

            ResponseService::success([
                'booking' => $booking
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:bookings,id',
                'computer_id' => 'nullable|integer|exists:computers,id',
                'status_id' => 'nullable|integer|exists:statuses,id',
                'client_id' => 'nullable|integer|exists:clients,id',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date',
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

            $model = Booking::query();

            $model->where('id', $data['id'])->update($data);

            $booking = $model->find($data['id']);

            ResponseService::success([
                'booking' => $booking
            ]);
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {

            $rules = [
                'id' => 'required|integer|exists:bookings,id',
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

            $model = Booking::query();

            $model->where('id', $request->id)->delete();

            ResponseService::success();
        } catch (\Throwable $th) {
            ResponseService::error(['message' => $th->getMessage()]);
        }
    }
}