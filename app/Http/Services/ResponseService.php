<?php

namespace App\Http\Services;

class ResponseService
{
    public static function success(?array $data = [], ?int $code = 200)
    {
        return response()->json([
            'success' => true,
            ...$data
        ], $code);
    }

    public static function error(?array $data = [], ?int $code = 500)
    {
        return response()->json([
            'success' => false,
            ...$data
        ], $code);
    }
}