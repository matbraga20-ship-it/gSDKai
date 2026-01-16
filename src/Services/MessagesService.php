<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Messages Service
 * Manage thread messages for Assistants.
 */
class MessagesService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a message in a thread.
     */
    public function create(string $threadId, array $payload): array
    {
        return $this->client->request("/threads/{$threadId}/messages", $payload, 'POST');
    }

    /**
     * List messages in a thread.
     */
    public function list(string $threadId, array $params = []): array
    {
        return $this->client->request("/threads/{$threadId}/messages", $params, 'GET');
    }

    /**
     * Retrieve a message from a thread.
     */
    public function retrieve(string $threadId, string $messageId): array
    {
        return $this->client->request("/threads/{$threadId}/messages/{$messageId}", [], 'GET');
    }
}
