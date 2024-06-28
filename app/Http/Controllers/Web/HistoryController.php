<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\History;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::with(['user', 'position'])->get();
        $historiesGroups = $histories->groupBy(function ($history) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $history->date)->locale('ru')->translatedFormat('Y-m-d');
        })->map(function($group) {
            return $group->map(function ($history) {
                return $history;
            });
        });
        return view('pages.history.index', [
            'historiesGroups' => $historiesGroups,
        ]);
    }
}