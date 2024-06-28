<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Position;

class RevisionController extends Controller
{
    public function index()
    {
        $positions = Position::query()->withSum('histories', 'count')->orderBy('sort', 'ASC')->get();

        return view('pages.revision.index', [
            'positions' => $positions
        ]);
    }
}