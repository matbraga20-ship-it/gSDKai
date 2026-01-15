<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;

class ImagesService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Generate images from a prompt
     * @param string $prompt
     * @param array $options
     * @return array
     * @throws OpenAIException
     */
    public function generate(string $prompt, array $options = []): array
    {
        $payload = array_merge([
            'prompt' => $prompt,
            'n' => $options['n'] ?? 1,
            'size' => $options['size'] ?? '1024x1024',
            'response_format' => $options['response_format'] ?? 'b64_json',
        ], $options['extra'] ?? []);

        try {
            $resp = $this->client->imagesCreate($payload);
            LoggerService::info('Image generation request', ['prompt_length' => strlen($prompt)]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Image generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
