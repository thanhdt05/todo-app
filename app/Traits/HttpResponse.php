<?php

namespace App\Traits;

trait HttpResponse {
    protected function success ($data = [], $message = null, $code = 200) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data = [], $message = null, $code) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);  
    }

    // Optional
    protected function notFound($message = 'Not Found') {
        return $this->error([], $message, 404);
    }

    protected function forbidden($message = 'Forbidden') {
        return $this->error([], $message, 403);
    }

    protected function unauthorized($message = 'Unauthorized') {
        return $this->error([], $message, 401);
    }
}