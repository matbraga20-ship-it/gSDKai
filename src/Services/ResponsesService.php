<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Responses Service
 * Create responses using the OpenAI Responses API.
 */
class ResponsesService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a response.
     */
    public function create(array $payload): array
    {
        return $this->client->responsesCreate($payload);
    }
}
