<?php

/**
 * OpenAI Content Toolkit REST API
 * Endpoints for text generation, chapters, and shorts ideas
 */

require_once dirname(__DIR__, 2) . '/bootstrap.php';

use OpenAI\Support\ResponseJson;
use OpenAI\Support\RateLimiter;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Exceptions\ValidationException;
use OpenAI\Exceptions\RateLimitException;
use OpenAI\Services\TextService;
use OpenAI\Services\ChaptersService;
use OpenAI\Services\ShortsIdeasService;
use OpenAI\Services\EmbeddingsService;
use OpenAI\Services\ImagesService;
use OpenAI\Services\AudioService;
use OpenAI\Services\ModelsService;
use OpenAI\Services\FilesService;
use OpenAI\Services\ModerationService;

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('/^\/api/', '', $path);
$path = preg_replace('/\/$/', '', $path) ?: '/';

try {
    // Health check endpoint (no rate limit)
    if ($path === '/health') {
        if ($method !== 'GET') {
            ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
        }

        $configValid = Config::validate();
        $storageWritable = is_writable(STORAGE_LOGS_PATH) && is_writable(STORAGE_CACHE_PATH);

        ResponseJson::send(ResponseJson::success([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'config_valid' => empty($configValid),
            'storage_writable' => $storageWritable,
            'api_key_configured' => Config::hasApiKey(),
        ]));
    }

    // Rate limiting for all other endpoints
    try {
        RateLimiter::check();
    } catch (RateLimitException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'RATE_LIMIT_EXCEEDED'),
            429
        );
    }

    // Get input depending on Content-Type. For application/json, parse JSON. For form submissions (including multipart), use $_POST/$_FILES.
    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    $input = [];

    if (stripos((string)$contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE && $method !== 'GET') {
            ResponseJson::send(
                ResponseJson::error('Invalid JSON in request body', 'INVALID_JSON'),
                400
            );
        }
        $input = $input ?? [];
    } else {
        // For form-encoded or multipart, prefer $_POST (files will be in $_FILES)
        $input = $_POST ?? [];
    }

    // Route requests
    match ($path) {
        '/generate/title' => handleGenerateTitle($method, $input),
        '/generate/description' => handleGenerateDescription($method, $input),
        '/generate/tags' => handleGenerateTags($method, $input),
        '/generate/timestamps' => handleGenerateTimestamps($method, $input),
        '/generate/shorts-ideas' => handleGenerateShortsIdeas($method, $input),
        '/embeddings' => handleEmbeddings($method, $input),
        '/images/generate' => handleImageGenerate($method, $input),
        '/audio/transcribe' => handleAudioTranscribe($method),
        '/models' => handleModelsList($method),
        '/files' => handleFilesList($method),
        '/files/upload' => handleFileUpload($method),
        '/moderation' => handleModeration($method, $input),
        '/openai/request' => handleOpenAIRequest($method, $input),
        default => ResponseJson::send(
            ResponseJson::error('Endpoint not found', 'NOT_FOUND'),
            404
        ),
    };
} catch (Exception $e) {
    LoggerService::error('API Error', [
        'path' => $path,
        'error' => $e->getMessage(),
    ]);

    ResponseJson::send(
        ResponseJson::error('Internal server error', 'SERVER_ERROR'),
        500
    );
}

function handleEmbeddings(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $text = $input['input'] ?? $input['text'] ?? null;
        if (empty($text)) {
            ResponseJson::send(ResponseJson::error('Input field is required', 'VALIDATION_ERROR'), 400);
        }

        $service = new OpenAI\Services\EmbeddingsService();
        $result = $service->create($text);

        ResponseJson::send(ResponseJson::success(['embeddings' => $result]));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleImageGenerate(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $prompt = $input['prompt'] ?? null;
        if (empty($prompt)) {
            ResponseJson::send(ResponseJson::error('Prompt is required', 'VALIDATION_ERROR'), 400);
        }

        $options = $input['options'] ?? [];
        $service = new OpenAI\Services\ImagesService();
        $result = $service->generate($prompt, $options);

        ResponseJson::send(ResponseJson::success(['images' => $result]));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleAudioTranscribe(string $method): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            ResponseJson::send(ResponseJson::error('Audio file is required', 'VALIDATION_ERROR'), 400);
        }

        // Move uploaded file to temp storage
        $tmpName = $_FILES['file']['tmp_name'];
        $dest = STORAGE_CACHE_PATH . '/uploads_' . bin2hex(random_bytes(6)) . '_' . basename($_FILES['file']['name']);
        if (!move_uploaded_file($tmpName, $dest)) {
            ResponseJson::send(ResponseJson::error('Failed to save uploaded file', 'SERVER_ERROR'), 500);
        }

        $options = [];
        if (!empty($_POST['language'])) {
            $options['language'] = $_POST['language'];
        }

        $service = new OpenAI\Services\AudioService();
        $result = $service->transcribe($dest, $options);

        ResponseJson::send(ResponseJson::success(['transcription' => $result]));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

// API Handlers

function handleGenerateTitle(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $content = $input['content'] ?? '';

        if (empty($content)) {
            ResponseJson::send(
                ResponseJson::error('Content field is required', 'VALIDATION_ERROR'),
                400
            );
        }

        $service = new TextService();
        $result = $service->generateTitle($content);

        ResponseJson::send(ResponseJson::success($result->toArray()));
    } catch (ValidationException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'VALIDATION_ERROR', null, ['errors' => $e->getErrors()]),
            400
        );
    } catch (OpenAIException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'),
            503
        );
    }
}

function handleGenerateDescription(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $content = $input['content'] ?? '';

        if (empty($content)) {
            ResponseJson::send(
                ResponseJson::error('Content field is required', 'VALIDATION_ERROR'),
                400
            );
        }

        $service = new TextService();
        $result = $service->generateDescription($content);

        ResponseJson::send(ResponseJson::success($result->toArray()));
    } catch (ValidationException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'VALIDATION_ERROR', null, ['errors' => $e->getErrors()]),
            400
        );
    } catch (OpenAIException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'),
            503
        );
    }
}

function handleGenerateTags(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $content = $input['content'] ?? '';

        if (empty($content)) {
            ResponseJson::send(
                ResponseJson::error('Content field is required', 'VALIDATION_ERROR'),
                400
            );
        }

        $service = new TextService();
        $result = $service->generateTags($content);

        ResponseJson::send(ResponseJson::success($result->toArray()));
    } catch (ValidationException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'VALIDATION_ERROR', null, ['errors' => $e->getErrors()]),
            400
        );
    } catch (OpenAIException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'),
            503
        );
    }
}

function handleGenerateTimestamps(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $transcript = $input['transcript'] ?? '';

        if (empty($transcript)) {
            ResponseJson::send(
                ResponseJson::error('Transcript field is required', 'VALIDATION_ERROR'),
                400
            );
        }

        $service = new ChaptersService();
        $result = $service->generateChapters($transcript);

        ResponseJson::send(ResponseJson::success($result->toArray()));
    } catch (ValidationException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'VALIDATION_ERROR', null, ['errors' => $e->getErrors()]),
            400
        );
    } catch (OpenAIException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'),
            503
        );
    }
}

function handleGenerateShortsIdeas(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $content = $input['content'] ?? '';
        $platform = $input['platform'] ?? 'tiktok';

        if (empty($content)) {
            ResponseJson::send(
                ResponseJson::error('Content field is required', 'VALIDATION_ERROR'),
                400
            );
        }

        $service = new ShortsIdeasService();
        $result = $service->generateIdeas($content, $platform);

        ResponseJson::send(ResponseJson::success($result->toArray()));
    } catch (ValidationException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'VALIDATION_ERROR', null, ['errors' => $e->getErrors()]),
            400
        );
    } catch (OpenAIException $e) {
        ResponseJson::send(
            ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'),
            503
        );
    }
}

function handleModelsList(string $method): void
{
    if ($method !== 'GET') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $service = new ModelsService();
        $result = $service->list();

        ResponseJson::send(ResponseJson::success($result));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleFilesList(string $method): void
{
    if ($method !== 'GET') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $service = new FilesService();
        $result = $service->list();

        ResponseJson::send(ResponseJson::success($result));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleFileUpload(string $method): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            ResponseJson::send(ResponseJson::error('File is required', 'VALIDATION_ERROR'), 400);
        }

        $tmpName = $_FILES['file']['tmp_name'];
        $originalName = basename($_FILES['file']['name']);
        $dest = STORAGE_CACHE_PATH . '/upload_' . bin2hex(random_bytes(6)) . '_' . $originalName;
        if (!move_uploaded_file($tmpName, $dest)) {
            ResponseJson::send(ResponseJson::error('Failed to save uploaded file', 'SERVER_ERROR'), 500);
        }

        $fields = [];
        $purpose = !empty($_POST['purpose']) ? (string)$_POST['purpose'] : '';
        if ($purpose !== '') {
            $fields['purpose'] = $purpose;
        }

        // Validation: fine-tune uploads must be .jsonl
        if (strtolower($purpose) === 'fine-tune' || strtolower($purpose) === 'fine_tune') {
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if ($ext !== 'jsonl') {
                // Remove temp file
                @unlink($dest);
                ResponseJson::send(ResponseJson::error('Invalid file format for fine-tune uploads. Must be .jsonl', 'VALIDATION_ERROR'), 400);
            }
        }

        $service = new FilesService();
        $result = $service->upload($dest, $fields);

        ResponseJson::send(ResponseJson::success($result));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleModeration(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $content = $input['input'] ?? $input['text'] ?? null;
        if (empty($content)) {
            ResponseJson::send(ResponseJson::error('Input is required', 'VALIDATION_ERROR'), 400);
        }

        $service = new ModerationService();
        $result = $service->moderate((array)$content);

        ResponseJson::send(ResponseJson::success($result));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}

function handleOpenAIRequest(string $method, array $input): void
{
    if ($method !== 'POST') {
        ResponseJson::send(ResponseJson::error('Method not allowed', 'METHOD_NOT_ALLOWED'), 405);
    }

    try {
        $endpoint = isset($input['endpoint']) ? trim((string)$input['endpoint']) : '';
        $httpMethod = strtoupper((string)($input['method'] ?? 'POST'));
        $payload = $input['payload'] ?? [];

        if ($endpoint === '' || strpos($endpoint, '/') !== 0) {
            ResponseJson::send(ResponseJson::error('Endpoint must start with "/"', 'VALIDATION_ERROR'), 400);
        }

        if (str_contains($endpoint, 'http')) {
            ResponseJson::send(ResponseJson::error('Full URLs are not allowed', 'VALIDATION_ERROR'), 400);
        }

        if (!in_array($httpMethod, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            ResponseJson::send(ResponseJson::error('Unsupported HTTP method', 'VALIDATION_ERROR'), 400);
        }

        if (!is_array($payload)) {
            ResponseJson::send(ResponseJson::error('Payload must be a JSON object', 'VALIDATION_ERROR'), 400);
        }

        $client = new OpenAI\Client\OpenAIClient();
        $result = $client->request($endpoint, $payload, $httpMethod);

        ResponseJson::send(ResponseJson::success($result));
    } catch (OpenAIException $e) {
        ResponseJson::send(ResponseJson::error($e->getMessage(), 'OPENAI_ERROR'), 503);
    }
}
