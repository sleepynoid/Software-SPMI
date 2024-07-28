<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function sendRespons($result, $message) {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
}
