<?php

namespace App\Http\Controllers\API;

use App\Exports\HistoryExport;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Position;
use App\Rules\SumNotNegative;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $model = History::with(['user', 'position']);

        if ($request->filled('date_from')) {
            $model->where('date', '<=', $request->date_from);
        }

        if ($request->filled('date_begin')) {
            $model->where('date', '>=', $request->date_begin);
        }

        if ($request->filled('position_id')) {
            $model->where('position_id', $request->position_id);
        }

        if ($request->filled('limit')) {
            $model->limit($request->limit);
        }

        if ($request->filled('offset')) {
            $model->offset($request->offset);
        }

        $histories = $model->orderBy('date', 'DESC')->get();

        $responce = [];

        // Вычисляем сумму за все время
        $total_sum = 0;
        if ($request->filled('position_id')) {
            $model->where('position_id', $request->position_id);
        }

        $total_sum = human_decimal($model->sum('count'));
        // Вычисляем сумму за определенную дату
        if ($request->filled('date_from')) {
            $sum = human_decimal($histories->where('date', '<', $request->date_from)->sum('count'));
        }

        $responce = [
            'total_sum' => $total_sum,
            'sum' => $sum ?? $total_sum,
            'histories' => $histories,
        ];


        if ($request->has('group_date')) {
            $historiesGroups = $histories->groupBy(function ($history) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $history->date)->locale('ru')->translatedFormat('Y-m-d');
            })->map(function ($group) {
                return $group->map(function ($history) {
                    return $history;
                });
            });
            unset($responce['histories']);
            $responce['groups'] = $historiesGroups;
        }

        return response()->json($responce);
    }

    public function show($id)
    {
        return History::query()->with(['position', 'user'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->merge([
            'is_revision' => $request->has('is_revision') ? 1 : 0,
            'user_id' => Auth::getUser()->id,
        ]);

        $rules = [
            'position_id' => 'required|integer|exists:positions,id',
            'count' => [
                'required',
                'decimal:0,2',
                'not_in:0',
            ],
            'date' => 'required|date',
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

        if($request->filled('count')) {

            $model = History::query();
            if($request->filled('position_id')) {
                $model->where('position_id', $request->position_id);
            }

            $sum = $model->sum('count'. function($q) {
                return $q->where('deleted_at IS NULL');
            });
            $res = human_decimal($sum + $request->count);
 
            if($res < 0) {
                return response()->json([
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        'count' => ["Сумма всех меньше 0 на $res"]
                    ]
                ], 422); // 422 - Unprocessable Entity
            } 
        }

        return History::create($request->all());
    }

    public function update($id, Request $request)
    {
        $request->merge([
            'id' => $id,
            'is_revision' => $request->has('is_revision') ? 1 : 0,
            'user_id' => Auth::getUser()->id,
        ]);

        $rules = [
            'id' => 'required|integer|exists:histories,id',
            'position_id' => 'nullable|integer|exists:positions,id',
            'count' => [
                'required',
                'decimal:0,2',
                'not_in:0',
            ],
            'date' => 'nullable|date',
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

        if($request->filled('count')) {

            $model = History::query()->whereNot('id', $request->id);
            if($request->filled('position_id')) {
                $model->where('position_id', $request->position_id);
            }

            $sum = $model->sum('count'. function($q) {
                return $q->where('deleted_at IS NULL');
            });
            $res = human_decimal($sum + $request->count);
 
            if($res < 0) {
                return response()->json([
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        'count' => ["Сумма всех меньше 0 на $res"]
                    ]
                ], 422); // 422 - Unprocessable Entity
            } 
        }

        History::query()->where('id', $id)->update($request->only((new History())->getFillable()));

        return response()->json([
            'history' => History::query()->with(['user', 'position'])->find($id),
        ]);
    }

    public function destroy($id)
    {
        $data = Validator::make([
            'id' => $id,
            'user_id' => Auth::getUser()->id,
        ], [
            'id' => 'required|integer|exists:histories,id',
            'user_id' => 'required|integer|exists:users,id'
        ])->validate();

        $history = History::query()->find($id);
        if ($history->user_id != $data['user_id']) {
            History::query()->update($data);
        }
        if (History::query()->where('id', $id)->delete($id)) {
            return ['status' => 'success'];
        }
        return ['status' => 'error'];
    }

    public function revisionBatch(Request $request)
    {
        $request->validate([
            'data.*.id' => 'required|integer|exists:positions,id',
            'data.*.count' => 'required|decimal:0,2',
        ]);

        $positions = Position::query()->withSum('histories', 'count')->whereIn('id', array_column($request->data, 'id'))->get();

        $data = [];
        $date = date('Y-m-d H:i:s');
        $positions->map(function ($p) use (&$data, $date, $request) {

            foreach ($request->data as $key => $value) {
                if ($value['id'] != $p->id) continue;

                $sum = $p->histories_sum_count ?? 0;

                $result = $value['count'] - $sum;

                if ($result == 0) {
                    continue;
                }

                $data[] = [
                    'position_id' => $p->id,
                    'is_revision' => 1,
                    'date' => $date,
                    'user_id' => Auth::id(),
                    'count' => $result,
                ];
            }
        });

        if (History::insert($data)) {
            return ['status' => 'success'];
        }

        return $data;
    }

    public function excel(Request $request)
    {
        $model = History::with(['user', 'position']);

        if ($request->filled('date_from')) {
            $model->where('date', '<=', $request->date_from);
        }

        if ($request->filled('date_begin')) {
            $model->where('date', '>=', $request->date_begin);
        }

        if ($request->filled('position_id')) {
            $model->where('position_id', $request->position_id);
        }

        $histories = $model->orderBy('date', 'DESC')->get();

        Excel::store(new HistoryExport($histories), 'history.xlsx', 'public');
    }
}
