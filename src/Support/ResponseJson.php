<?php

namespace OpenAI\Support;

/**
 * Standardized JSON Response Helper
 * Ensures all API responses follow a consistent format
 */
class ResponseJson
{
    /**
     * Success response
     */
    public static function success(mixed $data = null, array $meta = []): array
    {
        return [
            'success' => true,
            'data' => $data,
            'error' => null,
            'meta' => $meta,
        ];
    }

    /**
     * Error response
     */
    public static function error(string $message, string $code = 'ERROR', mixed $data = null, array $meta = []): array
    {
        return [
            'success' => false,
            'data' => $data,
            'error' => [
                'message' => $message,
                'code' => $code,
            ],
            'meta' => $meta,
        ];
    }

    /**
     * Send JSON response and exit
     */
    public static function send(array $response, int $httpCode = 200): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
}
