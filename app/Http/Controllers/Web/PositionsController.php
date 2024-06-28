<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Position;

class PositionsController extends Controller
{
    public function index()
    {
        $positions = Position::withTrashed()->orderBy('sort', 'ASC')->get()->groupBy(function ($record) {
            return $record->deleted_at ? 'deleted' : 'not_deleted';
        });

        return view('pages.positions.index', [
            'positions' => $positions,
        ]);
    }
}