<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('pages.users.index', [
            'users' => $users
        ]);
    }
}