<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\LoggerService;

class AudioService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Transcribe an audio file
     * @param string $filePath
     * @param array $options
     * @return array
     * @throws OpenAIException
     */
    public function transcribe(string $filePath, array $options = []): array
    {
        if (!is_file($filePath)) {
            throw new OpenAIException('Audio file not found: ' . $filePath);
        }

        $payload = array_merge([
            'file_path' => $filePath,
            'model' => $options['model'] ?? 'gpt-4o-transcribe',
            'language' => $options['language'] ?? null,
        ], $options['extra'] ?? []);

        try {
            $resp = $this->client->audioTranscribe($payload);
            LoggerService::info('Audio transcription done', ['file' => $filePath]);
            return $resp;
        } catch (OpenAIException $e) {
            LoggerService::error('Audio transcription failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
