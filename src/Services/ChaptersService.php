<?php

namespace OpenAI\Services;

use OpenAI\Client\OpenAIClient;
use OpenAI\DTO\ChaptersRequest;
use OpenAI\DTO\ChaptersResponse;
use OpenAI\DTO\Chapter;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Exceptions\ValidationException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;
use OpenAI\Support\Validator;

/**
 * Chapters/Timestamps Service
 * Generates chapters and timestamps from video transcripts
 */
class ChaptersService
{
    private OpenAIClient $client;

    public function __construct()
    {
        $this->client = new OpenAIClient();
    }

    /**
     * Generate chapters from transcript
     */
    public function generateChapters(string $transcript): ChaptersResponse
    {
        $this->validateInput($transcript, 'transcript', 50, 10000);

        LoggerService::info('Generating chapters', ['transcript_length' => strlen($transcript)]);

        $messages = PromptBuilder::chaptersPrompt($transcript);
        $response = $this->callOpenAI($messages);
        $content = $this->extractContent($response);
        $chapters = $this->parseChapters($content);

        return new ChaptersResponse(
            chapters: $chapters,
            model: Config::get('openai_model'),
            usage: $this->extractUsage($response),
        );
    }

    /**
     * Parse chapter format from text response
     *
     * Expected format:
     * [00:00:00] Chapter Title
     * [00:01:30] Next Chapter
     */
    private function parseChapters(string $content): array
    {
        $chapters = [];
        $lines = array_filter(array_map('trim', explode("\n", $content)));

        foreach ($lines as $line) {
            // Match pattern [HH:MM:SS] Title
            if (preg_match('/^\[(\d{2}:\d{2}:\d{2})\]\s+(.+)$/', $line, $matches)) {
                $chapters[] = new Chapter(
                    timestamp: $matches[1],
                    title: trim($matches[2]),
                );
            }
        }

        if (empty($chapters)) {
            LoggerService::warning('Could not parse chapters from response', [
                'response' => substr($content, 0, 200),
            ]);

            // Fallback: create chapters from content lines
            $lines = array_filter(array_map('trim', explode("\n", $content)), fn($l) => !empty($l));
            foreach ($lines as $index => $line) {
                $time = sprintf('%02d:%02d:%02d', $index * 5, 0, 0);
                // Remove any timestamp prefix if present
                $title = preg_replace('/^\[[^\]]+\]\s+/', '', $line);
                $chapters[] = new Chapter(timestamp: $time, title: $title);
            }
        }

        return array_slice($chapters, 0, 10); // Limit to 10 chapters
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
            LoggerService::error('Chapter generation failed', [
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
            $texts = [];
            foreach ($response['output'] as $item) {
                if (isset($item['content']) && is_array($item['content'])) {
                    foreach ($item['content'] as $c) {
                        if (isset($c['type']) && in_array($c['type'], ['output_text', 'text']) && isset($c['text'])) {
                            $texts[] = $c['text'];
                        }
                    }
                }
            }

            if (!empty($texts)) {
                return trim(implode("\n", $texts));
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
    private function validateInput(string $value, string $field, int $minLength = 1, int $maxLength = 10000): void
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
