<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Construir respuesta de éxito
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = "", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Construir respuesta de error
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, array $errors = [], int $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
