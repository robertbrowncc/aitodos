<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    protected function resourceResponse(JsonResource $resource, string $message = null, int $code = 200): JsonResponse
    {
        // Merge the resource response with our standard format
        return response()->json(array_merge(
            [
                'status' => 'success',
                'message' => $message,
            ],
            $resource->response()->getData(true)
        ), $code);
    }

    protected function resourceCollectionResponse(ResourceCollection $collection, string $message = null, int $code = 200): JsonResponse
    {
        // Merge the collection response with our standard format
        return response()->json(array_merge(
            [
                'status' => 'success',
                'message' => $message,
            ],
            $collection->response()->getData(true)
        ), $code);
    }

    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
