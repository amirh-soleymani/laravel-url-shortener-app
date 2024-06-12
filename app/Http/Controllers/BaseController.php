<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function responseSuccess(array $data, $message = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function responseError($message = null, array $errors = [], $statusCode = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
