<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Position;

class PositionController extends Controller
{
    public function index($id)
    {
        $date = date('Y-m-d H:i:s');
        $position = Position::with(['histories' => function($q) {
           $q->orderBy('date', 'DESC');
        }])->limit(30)->find($id);

        if ($position->histories->isNotEmpty()) {
            // Вычисляем сумму за все время
            $position->total_sum_count = human_decimal($position->histories->sum(fn($history) => $history->count) ?? 0);
            // Вычисляем сумму за определенную дату
            $position->sum_count = human_decimal($position->histories->where('date', '<', $date)->sum('count') ?? 0);
        } else {
            $position->total_sum_count = 0;
            $position->sum_count = 0;
        }

        return view('pages.position.index', [
            'position' => $position,
        ]);
    }
}