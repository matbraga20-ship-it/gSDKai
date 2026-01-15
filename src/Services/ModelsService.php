<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\LoggerService;

class ModelsService
{
    private OpenAIClient $client;

    public function __construct(?OpenAIClient $client = null)
    {
        $this->client = $client ?? new OpenAIClient();
    }

    public function list(): array
    {
        try {
            $resp = $this->client->modelsList();
            LoggerService::info('Models listed', ['count' => count($resp['data'] ?? [])]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Models list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
