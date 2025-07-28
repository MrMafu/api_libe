<?php

namespace App\Http\Api;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Format API response
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return JsonResponse
     */
    public static function format(
        bool $success,
        string $message = "",
        mixed $data = null,
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            "success" => $success,
            "message" => $message,
            "data"    => $data,
        ], $status);
    }

    /**
     * Format successful API response
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = "",
        int $status = 200,
    ): JsonResponse {
        return self::format(true, $message, $data, $status);
    }

    /**
     * Format error API response
     *
     * @param string $message
     * @param int $status
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function error(
        string $message = "",
        int $status = 400,
        mixed $data = null,
    ): JsonResponse {
        return self::format(false, $message, $data, $status);
    }

    /**
     * No content API response
     * 
     * @return JsonResponse
    */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}