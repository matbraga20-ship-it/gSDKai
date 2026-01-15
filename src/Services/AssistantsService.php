<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Assistants Service
 * Wraps the Assistants API endpoints.
 */
class AssistantsService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a new assistant.
     */
    public function create(array $payload): array
    {
        return $this->client->request('/assistants', $payload, 'POST');
    }

    /**
     * List assistants.
     */
    public function list(array $params = []): array
    {
        return $this->client->request('/assistants', $params, 'GET');
    }

    /**
     * Retrieve a specific assistant.
     */
    public function retrieve(string $assistantId): array
    {
        return $this->client->request("/assistants/{$assistantId}", [], 'GET');
    }

    /**
     * Update an assistant.
     */
    public function update(string $assistantId, array $payload): array
    {
        return $this->client->request("/assistants/{$assistantId}", $payload, 'POST');
    }

    /**
     * Delete an assistant.
     */
    public function delete(string $assistantId): array
    {
        return $this->client->request("/assistants/{$assistantId}", [], 'DELETE');
    }
}
