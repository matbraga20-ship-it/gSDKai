<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\DTO\TextGenerationRequest;
use OpenAI\DTO\TextGenerationResponse;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Exceptions\ValidationException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;
use OpenAI\Support\Validator;

/**
 * Text Generation Service
 * Generates titles, descriptions, tags, and other text content
 */
class TextService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Generate a title from content
     */
    public function generateTitle(string $content): TextGenerationResponse
    {
        $this->validateInput($content, 'content', 10, 5000);

        LoggerService::info('Generating title', ['content_length' => strlen($content)]);

        $messages = PromptBuilder::titlePrompt($content);
        $response = $this->callOpenAI($messages);
        $title = $this->extractContent($response);

        return new TextGenerationResponse(
            result: $title,
            type: 'title',
            model: Config::get('openai_model'),
            usage: $this->extractUsage($response),
        );
    }

    /**
     * Generate a description from content
     */
    public function generateDescription(string $content): TextGenerationResponse
    {
        $this->validateInput($content, 'content', 10, 5000);

        LoggerService::info('Generating description', ['content_length' => strlen($content)]);

        $messages = PromptBuilder::descriptionPrompt($content);
        $response = $this->callOpenAI($messages);
        $description = $this->extractContent($response);

        return new TextGenerationResponse(
            result: $description,
            type: 'description',
            model: Config::get('openai_model'),
            usage: $this->extractUsage($response),
        );
    }

    /**
     * Generate tags from content
     */
    public function generateTags(string $content): TextGenerationResponse
    {
        $this->validateInput($content, 'content', 10, 5000);

        LoggerService::info('Generating tags', ['content_length' => strlen($content)]);

        $messages = PromptBuilder::tagsPrompt($content);
        $response = $this->callOpenAI($messages);
        $tags = $this->extractContent($response);

        return new TextGenerationResponse(
            result: $tags,
            type: 'tags',
            model: Config::get('openai_model'),
            usage: $this->extractUsage($response),
        );
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
            LoggerService::error('Text generation failed', [
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
        // Responses API: prefer 'output_text' top-level helper
        if (isset($response['output_text'])) {
            return trim((string)$response['output_text']);
        }

        // Otherwise, scan output items for message content
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

        // Fallback to legacy choices
        if (isset($response['choices']) && is_array($response['choices']) && count($response['choices']) > 0) {
            $choice = $response['choices'][0];
            if (isset($choice['message']['content'])) {
                return trim($choice['message']['content']);
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
}
