<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;

class EmbeddingsService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Create embeddings for given input (string or array)
     * @param string|array $input
     * @param string|null $model
     * @return array
     * @throws OpenAIException
     */
    public function create($input, ?string $model = null): array
    {
        $payload = [
            'model' => $model ?? Config::get('openai_embedding_model', 'text-embedding-3-small'),
            'input' => $input,
        ];

        try {
            $resp = $this->client->embeddingsCreate($payload);
            LoggerService::info('Embeddings created', ['model' => $payload['model']]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Embeddings creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
