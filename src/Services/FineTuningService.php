<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Fine-Tuning Service
 * Manage fine-tuning jobs and events.
 */
class FineTuningService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a fine-tuning job.
     */
    public function createJob(array $payload): array
    {
        return $this->client->request('/fine_tuning/jobs', $payload, 'POST');
    }

    /**
     * List fine-tuning jobs.
     */
    public function listJobs(array $params = []): array
    {
        return $this->client->request('/fine_tuning/jobs', $params, 'GET');
    }

    /**
     * Retrieve a fine-tuning job.
     */
    public function retrieveJob(string $jobId): array
    {
        return $this->client->request("/fine_tuning/jobs/{$jobId}", [], 'GET');
    }

    /**
     * Cancel a fine-tuning job.
     */
    public function cancelJob(string $jobId): array
    {
        return $this->client->request("/fine_tuning/jobs/{$jobId}/cancel", [], 'POST');
    }

    /**
     * List fine-tuning events.
     */
    public function listEvents(string $jobId, array $params = []): array
    {
        return $this->client->request("/fine_tuning/jobs/{$jobId}/events", $params, 'GET');
    }
}
