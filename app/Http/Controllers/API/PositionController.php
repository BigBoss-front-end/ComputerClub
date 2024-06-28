<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $model = Position::query()->with(['histories' => function($q) {
            return $q->where('date', '<=', date('Y-m-d H:i:s'));
        }])->orderBy('sort', 'ASC');

        $positions = $model->get();

        $date = $request->has('history_date_from') ? $request->history_date_from : date('Y-m-d H:i:s');
        $positions->each(function($position) use ($date) {
            if ($position->histories->isNotEmpty()) {
                $position->total_sum_count = human_decimal($position->histories->sum('count'));
                $position->sum_count = human_decimal($position->histories->where('date', '<', $date)->sum('count'));
            } else {
                $position->total_sum_count = 0;
                $position->sum_count = 0;
            }
        });
        
        return response()->json([
            'positions' => $positions,
            'total_count' => human_decimal(
                $positions->sum(fn($p) => $p->total_sum_count), 2
            ),
            'count' => human_decimal(
                $positions->sum(fn($p) => $p->sum_count), 2
            ),
        ]);
    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sort' => 'nullable|integer',
                'beginning_balance' => 'nullable|decimal:0,2',
            ]);
    
            $position =  Position::create($request->all())->fresh();

            if($request->filled('beginning_balance') && $request->beginning_balance !=0) {
                History::query()->create([
                    'position_id' => $position->id,
                    'date' => date('Y-m-d H:i:s'),
                    'count' => $request->beginning_balance,
                    'user_id' => Auth::id(),
                    'is_revision' => 1,
                ]);
            }
            
            return response()->json([
                'message' => 'Данные успешно обновлены',
                'position' => $position,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Произошла ошибка при сохранении данных'], 500);
        }

    }

    public function update($id, Request $request)
    {
        $request->merge([
            'id' => $id,
        ]);

        $request->validate([
            'id' => 'required|integer|exists:positions,id',
            'name' => 'nullable|string|max:255',
            'sort' => 'nullable|integer',
        ]);

        Position::query()->update($request->all());

        if($request->has('revision')) {
            Validator::make([
                'revision' => $request->revision,
            ])->validate([
                'revision' => 'nullable|decimal:2',
            ]);

            $sumHistory = History::query()->sum('count');

            $result = $sumHistory - $request->revision;

            History::query()->create([
                'position_id' => $id,
                'count' => $result,
                'is_revision' => 1,
                'user_id' => Auth::id(),
                'date' => date('Y-m-d'),
            ]);
        }

        return Position::query()->find($id);
    }

    public function updateBatch(Request $request)
    {
        try {
            $request->validate([
                'data.*.id' => 'required|integer|exists:positions,id',
                'data.*.name' => 'nullable|string|max:255',
                'data.*.sort' => 'nullable|integer',
            ]);

            $currentDate = date('Y-m-d H:i:s');
    
            Position::query()->upsert($request->data, 'id');

            return response()->json([
                'message' => 'Данные успешно обновлены',
                'positions' => Position::query()->where('updated_at', '>=', $currentDate)->get(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Validator::make([
            'id' => $id,
        ], [
            'id' => 'required|integer|exists:positions,id',
        ])->validate();


        // Начинаем транзакцию
        DB::beginTransaction();

        try {
            // Ваш код для выполнения операций с базой данных
            Position::query()->where('id', $id)->delete($id);

            History::query()->where('position_id', $id)->where('deleted_at IS NULL')->update([
                'deleted_with_main' => true,
            ]);
            History::query()->where('position_id', $id)->delete();
            
            // Если все операции успешны, коммитим транзакцию
            DB::commit();

            return response()->json([
                'message' => 'Данные успешно обновлены',
                'position' => Position::withTrashed()->find($id),
            ], 200);
        } catch (\Exception $e) {
            // Если произошла ошибка, откатываем транзакцию
            DB::rollback();

            return response()->json(['message' => 'Произошла ошибка при сохранении данных'], 500);
        }
    }

    public function restore($id)
    {
        Validator::make([
            'id' => $id,
        ], [
            'id' => 'required|integer|exists:positions,id',
        ])->validate();


        // Начинаем транзакцию
        DB::beginTransaction();

        try {
            // Ваш код для выполнения операций с базой данных
            Position::query()->where('id', $id)->restore();

            History::query()->where('position_id', $id)->where('deleted_with_main', true)->restore();

            History::query()->where('position_id', $id)->update([
                'deleted_with_main' => false,
            ]);
            
            // Если все операции успешны, коммитим транзакцию
            DB::commit();

            return response()->json([
                'message' => 'Данные успешно обновлены',
                'position' => Position::query()->find($id),
            ], 200);
        } catch (\Exception $e) {
            // Если произошла ошибка, откатываем транзакцию
            DB::rollback();

            return response()->json(['message' => 'Произошла ошибка при сохранении данных'], 500);
        }
    }
}
