<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Runs Service
 * Manage runs for Assistants threads.
 */
class RunsService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a run for a thread.
     */
    public function create(string $threadId, array $payload): array
    {
        return $this->client->request("/threads/{$threadId}/runs", $payload, 'POST');
    }

    /**
     * Retrieve a run.
     */
    public function retrieve(string $threadId, string $runId): array
    {
        return $this->client->request("/threads/{$threadId}/runs/{$runId}", [], 'GET');
    }

    /**
     * List runs for a thread.
     */
    public function list(string $threadId, array $params = []): array
    {
        return $this->client->request("/threads/{$threadId}/runs", $params, 'GET');
    }

    /**
     * Cancel a run.
     */
    public function cancel(string $threadId, string $runId): array
    {
        return $this->client->request("/threads/{$threadId}/runs/{$runId}/cancel", [], 'POST');
    }

    /**
     * Submit tool outputs for a run.
     */
    public function submitToolOutputs(string $threadId, string $runId, array $payload): array
    {
        return $this->client->request("/threads/{$threadId}/runs/{$runId}/submit_tool_outputs", $payload, 'POST');
    }

    /**
     * List run steps.
     */
    public function listSteps(string $threadId, string $runId, array $params = []): array
    {
        return $this->client->request("/threads/{$threadId}/runs/{$runId}/steps", $params, 'GET');
    }
}
