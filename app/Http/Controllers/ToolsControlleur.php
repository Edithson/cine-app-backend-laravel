<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolsControlleur extends Controller
{
    public static function successResponse($data, $message, $meta = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'meta' => array_merge([
                'timestamp' => now()->toISOString()
            ], $meta)
        ], $status);
    }

    public static function errorResponse($message, $status = 500, $data = [])
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
            'meta' => [
                'timestamp' => now()->toISOString()
            ]
        ], $status);
    }
}
