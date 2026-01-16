<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Vector Stores Service
 * Manage vector stores and their files.
 */
class VectorStoresService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a vector store.
     */
    public function create(array $payload): array
    {
        return $this->client->request('/vector_stores', $payload, 'POST');
    }

    /**
     * List vector stores.
     */
    public function list(array $params = []): array
    {
        return $this->client->request('/vector_stores', $params, 'GET');
    }

    /**
     * Retrieve a vector store.
     */
    public function retrieve(string $storeId): array
    {
        return $this->client->request("/vector_stores/{$storeId}", [], 'GET');
    }

    /**
     * Update a vector store.
     */
    public function update(string $storeId, array $payload): array
    {
        return $this->client->request("/vector_stores/{$storeId}", $payload, 'POST');
    }

    /**
     * Delete a vector store.
     */
    public function delete(string $storeId): array
    {
        return $this->client->request("/vector_stores/{$storeId}", [], 'DELETE');
    }

    /**
     * List files in a vector store.
     */
    public function listFiles(string $storeId, array $params = []): array
    {
        return $this->client->request("/vector_stores/{$storeId}/files", $params, 'GET');
    }

    /**
     * Add a file to a vector store.
     */
    public function addFile(string $storeId, array $payload): array
    {
        return $this->client->request("/vector_stores/{$storeId}/files", $payload, 'POST');
    }

    /**
     * Retrieve a vector store file.
     */
    public function retrieveFile(string $storeId, string $fileId): array
    {
        return $this->client->request("/vector_stores/{$storeId}/files/{$fileId}", [], 'GET');
    }

    /**
     * Delete a vector store file.
     */
    public function deleteFile(string $storeId, string $fileId): array
    {
        return $this->client->request("/vector_stores/{$storeId}/files/{$fileId}", [], 'DELETE');
    }
}
