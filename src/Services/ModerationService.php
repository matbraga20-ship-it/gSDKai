<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\LoggerService;

class ModerationService
{
    private OpenAIClient $client;

    public function __construct(?OpenAIClient $client = null)
    {
        $this->client = $client ?? new OpenAIClient();
    }

    public function moderate(array $input): array
    {
        try {
            $resp = $this->client->moderationCreate(['input' => $input]);
            LoggerService::info('Moderation requested');
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Moderation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
