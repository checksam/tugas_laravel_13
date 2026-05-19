<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Standardized JSON Response Format.
 *
 * All API responses will follow this structure:
 * {
 *     "success": true|false,
 *     "message": "...",
 *     "data": {...}|[...],
 *     "errors": null|{...},
 *     "meta": null|{ "pagination": {...}, ... }
 * }
 */
trait JsonResponseTrait
{
    /**
     * Return a successful JSON response.
     *
     * @param  array<string, mixed>|object|null  $data
     * @param  array<string, mixed>|null  $meta
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Berhasil.',
        int $statusCode = 200,
        ?array $meta = null,
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => $meta,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param  array<string, mixed>|null  $errors
     */
    protected function errorResponse(
        string $message = 'Terjadi kesalahan.',
        int $statusCode = 400,
        ?array $errors = null,
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta' => null,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated JSON response with standardized meta.
     *
     * @param  array<string, mixed>|null  $additionalMeta
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = 'Data berhasil dimuat.',
        ?array $additionalMeta = null,
    ): JsonResponse {
        $meta = [
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];

        if ($additionalMeta) {
            $meta = array_merge($meta, $additionalMeta);
        }

        return $this->successResponse(
            data: $paginator->items(),
            message: $message,
            meta: $meta,
        );
    }

    /**
     * Return a "not found" JSON response.
     */
    protected function notFoundResponse(string $message = 'Data tidak ditemukan.'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return a "conflict" JSON response (409).
     */
    protected function conflictResponse(string $message): JsonResponse
    {
        return $this->errorResponse($message, 409);
    }

    /**
     * Return a "created" JSON response (201).
     *
     * @param  array<string, mixed>|object|null  $data
     */
    protected function createdResponse(mixed $data = null, string $message = 'Data berhasil dibuat.'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }
}
