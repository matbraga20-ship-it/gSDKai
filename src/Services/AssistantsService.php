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
     * Create an assistant with tools, file attachments, and metadata helpers.
     */
    public function createWithConfig(
        string $name,
        string $model,
        string $instructions = '',
        array $tools = [],
        array $fileIds = [],
        array $metadata = []
    ): array {
        $payload = $this->buildConfigPayload($name, $model, $instructions, $tools, $fileIds, $metadata);

        return $this->create($payload);
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
     * Update an assistant using tools, file attachments, and metadata helpers.
     */
    public function updateWithConfig(
        string $assistantId,
        string $name,
        string $model,
        string $instructions = '',
        array $tools = [],
        array $fileIds = [],
        array $metadata = []
    ): array {
        $payload = $this->buildConfigPayload($name, $model, $instructions, $tools, $fileIds, $metadata);

        return $this->update($assistantId, $payload);
    }

    /**
     * Delete an assistant.
     */
    public function delete(string $assistantId): array
    {
        return $this->client->request("/assistants/{$assistantId}", [], 'DELETE');
    }

    private function buildConfigPayload(
        string $name,
        string $model,
        string $instructions,
        array $tools,
        array $fileIds,
        array $metadata
    ): array {
        $payload = [
            'name' => $name,
            'model' => $model,
        ];

        if ($instructions !== '') {
            $payload['instructions'] = $instructions;
        }

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        if (!empty($fileIds)) {
            $payload['file_ids'] = $fileIds;
        }

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;
        }

        return $payload;
    }
}
