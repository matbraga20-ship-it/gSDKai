<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Threads Service
 * Wraps the Threads API endpoints used by Assistants.
 */
class ThreadsService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a new thread.
     */
    public function create(array $payload = []): array
    {
        return $this->client->request('/threads', $payload, 'POST');
    }

    /**
     * Retrieve a thread.
     */
    public function retrieve(string $threadId): array
    {
        return $this->client->request("/threads/{$threadId}", [], 'GET');
    }

    /**
     * Update a thread.
     */
    public function update(string $threadId, array $payload): array
    {
        return $this->client->request("/threads/{$threadId}", $payload, 'POST');
    }

    /**
     * Delete a thread.
     */
    public function delete(string $threadId): array
    {
        return $this->client->request("/threads/{$threadId}", [], 'DELETE');
    }
}
