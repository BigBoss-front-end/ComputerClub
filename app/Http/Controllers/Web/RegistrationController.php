<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('pages/registration/index');
    }
}