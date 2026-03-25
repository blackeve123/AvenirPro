<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Standardized Success Response
     */
    protected function successResponse($data = null, string $message = null, int $code = 200): JsonResponse
    {
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            return $data->additional([
                'success' => true,
                'message' => $message,
            ])->response()->setStatusCode($code);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Standardized Error Response
     */
    protected function errorResponse(string $message = null, int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
