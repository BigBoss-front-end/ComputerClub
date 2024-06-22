<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class RecoveryController extends Controller
{
    public function index()
    {
        return view('pages/recovery/index');
    }
}