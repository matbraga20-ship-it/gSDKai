<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\DTO\ShortsIdeasRequest;
use OpenAI\DTO\ShortsIdeasResponse;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Exceptions\ValidationException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;
use OpenAI\Support\Validator;

/**
 * Shorts Ideas Service
 * Generates short-form video ideas for TikTok, Instagram Reels, YouTube Shorts
 */
class ShortsIdeasService
{
    private OpenAIClient $client;

    private const VALID_PLATFORMS = ['tiktok', 'reels', 'shorts'];

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Generate shorts ideas for a platform
     */
    public function generateIdeas(string $content, string $platform = 'tiktok'): ShortsIdeasResponse
    {
        $this->validateInput($content, 'content', 20, 5000);
        $this->validatePlatform($platform);

        LoggerService::info('Generating shorts ideas', [
            'platform' => $platform,
            'content_length' => strlen($content),
        ]);

        $messages = PromptBuilder::shortsPrompt($content, $platform);
        $response = $this->callOpenAI($messages);
        $content = $this->extractContent($response);
        $ideas = $this->parseIdeas($content);

        return new ShortsIdeasResponse(
            ideas: $ideas,
            platform: $platform,
            model: Config::get('openai_model'),
            usage: $this->extractUsage($response),
        );
    }

    /**
     * Parse ideas from numbered list format
     */
    private function parseIdeas(string $content): array
    {
        $ideas = [];
        $lines = array_filter(array_map('trim', explode("\n", $content)));

        foreach ($lines as $line) {
            // Remove numbering if present (1. 2. etc.)
            $idea = preg_replace('/^\d+\.\s+/', '', $line);
            $idea = trim($idea);

            if (!empty($idea)) {
                $ideas[] = $idea;
            }
        }

        // Limit to 8 ideas
        return array_slice($ideas, 0, 8);
    }

    /**
     * Call OpenAI API with proper configuration
     */
    private function callOpenAI(array $messages): array
    {
        $payload = [
            'model' => Config::get('openai_model', 'gpt-4o-mini'),
            'input' => $messages,
            'temperature' => (float) Config::get('openai_temperature', 0.7),
            'max_output_tokens' => (int) Config::get('openai_max_output_tokens', 800),
        ];

        try {
            return $this->client->responsesCreate($payload);
        } catch (OpenAIException $e) {
            LoggerService::error('Shorts ideas generation failed', [
                'error' => $e->getMessage(),
            ]);
            Config::recordError($e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract text content from OpenAI response
     */
    private function extractContent(array $response): string
    {
        if (isset($response['output_text'])) {
            return trim((string)$response['output_text']);
        }

        if (isset($response['output']) && is_array($response['output'])) {
            foreach ($response['output'] as $item) {
                if (isset($item['content']) && is_array($item['content'])) {
                    foreach ($item['content'] as $c) {
                        if (isset($c['type']) && in_array($c['type'], ['output_text', 'text']) && isset($c['text'])) {
                            return trim($c['text']);
                        }
                    }
                }
            }
        }

        throw new OpenAIException('No text content in OpenAI response');
    }

    /**
     * Extract token usage from response
     */
    private function extractUsage(array $response): ?int
    {
        return $response['usage']['total_tokens'] ?? null;
    }

    /**
     * Validate input string
     */
    private function validateInput(string $value, string $field, int $minLength = 1, int $maxLength = 5000): void
    {
        $validator = new Validator();
        $validator->required($value, $field)
            ->minLength($value, $minLength, $field)
            ->maxLength($value, $maxLength, $field);

        if (!$validator->passes()) {
            throw new ValidationException(
                'Validation failed',
                $validator->errors()
            );
        }
    }

    /**
     * Validate platform
     */
    private function validatePlatform(string $platform): void
    {
        if (!in_array($platform, self::VALID_PLATFORMS, true)) {
            throw new ValidationException(
                'Invalid platform: ' . $platform,
                ['platform' => 'Must be one of: ' . implode(', ', self::VALID_PLATFORMS)]
            );
        }
    }
}
