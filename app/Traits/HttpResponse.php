<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponse
{
    protected function success(
        mixed $data = [],
        ?string $message = null,
        int $code = 200
    ) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(
        ?string $message = null,
        int $code = 400,
        mixed $errors = null
    ) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function paginated(
        ResourceCollection $collection,
        ?string $message = null
    ) {
        $response = $collection->toResponse(request())->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $response['data'] ?? [],
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ], 200);
    }
}
