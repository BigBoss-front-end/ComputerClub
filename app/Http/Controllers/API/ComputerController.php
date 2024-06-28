<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\ResponseService;
use App\Models\Booking;
use App\Models\Computer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'filter.date_free_from' => 'nullable|date|date_format:Y-m-d H:i:s',
                'filter.date_free_to' => 'nullable|date|date_format:Y-m-d H:i:s',
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

            if ($request->filled('filter.name')) {
                $model->where('name', 'LIKE', '%' . $request->input('filter.name') . '%');
            }

            if ($request->filled('filter.sort')) {
                $model->where('sort', $request->input('filter.sort'));
            }

            if ($request->filled('filter.user_id')) {
                $model->where('user_id', $request->input('filter.user_id'));
            }

            if ($request->filled('with')) {
                $model->with($request->input('with'));
            }

            $model->orderBy('sort');

            $computers = $model->paginate(100, ['*'], 'page', $request->filled('filter.page') ? $request->input('filter.page') : 1);

            $collection = $computers->getCollection();
            \App\Http\Services\ComputerService::setStatusesAndClients($collection)::setNearest($collection)::setFreeTimes($collection);

            if ($request->filled('filter.status_id')) {
                $collection = $collection->filter(function ($computer) use ($request) {
                    return $request->input('filter.status_id') == $computer->status->id;
                });
            }

            if ($request->filled('filter.client_name')) {
                $collection = $collection->filter(function ($computer) use ($request) {
                    if (empty($computer->client)) {
                        return false;
                    }
                    $currrentName = mb_strtolower($computer->client->name);
                    $name = mb_strtolower($request->input('filter.client_name'));
                    return stripos($currrentName, $name) !== false ||
                        stripos($computer->client->phone, $name) !== false ||
                        stripos($computer->client->email, $name) !== false;
                });
            }


            if ($request->filled('filter.date_free_from') || $request->filled('filter.date_free_to')) {
                $model = Booking::query();
                $model->where(function ($q) use ($request) {
                    $q->where(function ($sq) use ($request) {
                        $sq->where('end_time', '>', $request->input('filter.date_free_from'))->where('start_time', '<', $request->input('filter.date_free_from'));
                    });
                
                    $q->orWhere(function ($sq) use ($request) {
                        $sq->where('start_time', '>', $request->input('filter.date_free_from'));
                    });
                });

                if ($request->filled('filter.date_free_to')) {
                    $model->where(function($q) use($request) {
                        $q->where(function ($sq) use ($request) {
                            $sq->where('end_time', '<', $request->input('filter.date_free_to'));
                        });
                        $q->orWhere(function ($sq) use ($request) {
                            $sq->where('start_time', '<', $request->input('filter.date_free_to'));
                        });
                    });
                }

                $notFreeBookings = $model->get();

                $collection = $collection->filter(function ($computer) use ($notFreeBookings) {
                    return !in_array($computer->id, $notFreeBookings->pluck('computer_id')->toArray());
                });
            }


            $computers->setCollection($collection->values());
            return ResponseService::success([
                'computers' => $computers,
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage(), 'line' => $th->getLine(), 'trace' => $th->getTrace()]);
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

            $model->with(['bookings']);

            $computer = $model->find($request->id);

            $collection = new Collection([$computer]);

            \App\Http\Services\ComputerService::setStatusesAndClients($collection)::setNearest($collection)::setFreeTimes($collection);
            return ResponseService::success([
                'computer' => $collection->first()
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
                'name' => !$request->filled('name') ? 'Новый компьютер' : $request->status,
                'status'  => !$request->filled('status') ? 'free' : $request->status,
                'user_id' => Auth::id(),
            ]);

            $rules = [
                'name' => 'required|string',
                'sort' => 'nullable|integer',
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

            $collection = new Collection([$computer]);

            \App\Http\Services\ComputerService::setStatusesAndClients($collection)::setNearest($collection)::setFreeTimes($collection);
            return ResponseService::success([
                'computer' => $collection->first(),
                'message' => 'Компьютер создан'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
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

            $collection = new Collection([$computer]);

            \App\Http\Services\ComputerService::setStatusesAndClients($collection)::setNearest($collection)::setFreeTimes($collection);
            return ResponseService::success([
                'computer' => $collection->first(),
                'message' => 'Компьютер обновлён'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }

    public function updateBatch(Request $request)
    {
        try {

            $rules = [
                '*.id' => 'required|integer|exists:computers,id',
                '*.name' => 'nullable|string',
                '*.sort' => 'nullable|integer',
                '*.status_id' => 'nullable|integer|exists:statuses,id',
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

            DB::transaction(function () use ($data) {
                foreach ($data as $key => $value) {
                    Computer::query()->where('id', $value['id'])->update($value);
                }
            });

            return ResponseService::success();
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
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

            return ResponseService::success([
                'message' => 'Компьютер удален'
            ]);
        } catch (\Throwable $th) {
            return ResponseService::error(['message' => $th->getMessage()]);
        }
    }
}
