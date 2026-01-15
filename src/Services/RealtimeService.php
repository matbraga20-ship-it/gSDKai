<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;

/**
 * Realtime Service
 * Create ephemeral sessions for Realtime API.
 */
class RealtimeService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create a realtime session (ephemeral key).
     */
    public function createSession(array $payload = []): array
    {
        return $this->client->request('/realtime/sessions', $payload, 'POST');
    }
}
