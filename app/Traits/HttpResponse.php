<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponse
{
    protected function success(
        mixed $data = null,
        ?string $message = null,
        int $code = 200
    ): JsonResponse {
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
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $code);
    }

    protected function paginated(
        ResourceCollection $collection,
        ?string $message = null
    ): JsonResponse {
        $response = $collection
            ->toResponse(request())
            ->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $response['data'] ?? [],
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ]);
    }
}
