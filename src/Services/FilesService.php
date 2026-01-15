<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\LoggerService;

class FilesService
{
    private OpenAIClient $client;

    public function __construct(?OpenAIClient $client = null)
    {
        $this->client = $client ?? new OpenAIClient();
    }

    public function list(): array
    {
        try {
            $resp = $this->client->filesList();
            LoggerService::info('Files listed', ['count' => count($resp['data'] ?? [])]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Files list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function upload(string $filePath, array $fields = []): array
    {
        try {
            $resp = $this->client->filesUpload($filePath, $fields);
            LoggerService::info('File uploaded', ['file' => $filePath]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('File upload failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
