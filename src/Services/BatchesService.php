<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Batches Service
 * Manage batch requests.
 */
class BatchesService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a batch.
     */
    public function create(array $payload): array
    {
        return $this->client->request('/batches', $payload, 'POST');
    }

    /**
     * List batches.
     */
    public function list(array $params = []): array
    {
        return $this->client->request('/batches', $params, 'GET');
    }

    /**
     * Retrieve a batch.
     */
    public function retrieve(string $batchId): array
    {
        return $this->client->request("/batches/{$batchId}", [], 'GET');
    }

    /**
     * Cancel a batch.
     */
    public function cancel(string $batchId): array
    {
        return $this->client->request("/batches/{$batchId}/cancel", [], 'POST');
    }
}
