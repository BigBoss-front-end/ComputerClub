<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Position;

class BalanceController extends Controller
{
    public function index()
    {
        $date = date('Y-m-d HH:MM:DD');
        $positions = Position::with('histories')->orderBy('sort', 'ASC')->get();

        $positions->map(function($position) use($date) {
            if ($position->histories->isNotEmpty()) {

                // dump($position->histories->sum(fn($history) => $history->count) ?? 0);
                // dump($position->histories->where('date', '<', $date)->sum('count') ?? 0);
                // Вычисляем сумму за все время
                $position->total_sum_count = human_decimal($position->histories->sum(fn($history) => $history->count) ?? 0);
                // Вычисляем сумму за определенную дату
                $position->sum_count = human_decimal($position->histories->where('date', '<', $date)->sum('count') ?? 0);
            } else {
                $position->total_sum_count = 0;
                $position->sum_count = 0;
            }
            return $position;
        });

        return view('pages.balance.index', [
            'positions' => $positions,
        ]);
    }
}