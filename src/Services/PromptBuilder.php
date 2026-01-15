<?php

namespace OpenAI\Services;

/**
 * Prompt Builder
 * Creates safe, templated prompts for various content generation tasks
 */
class PromptBuilder
{
    /**
     * Build title generation prompt
     */
    public static function titlePrompt(string $content): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'You are an expert copywriter specializing in creating compelling, SEO-friendly titles. '
                    . 'Generate exactly ONE title (40-60 characters) that is catchy, clear, and optimized for search engines. '
                    . 'Do not include quotes or extra text, only the title.',
            ],
            [
                'role' => 'user',
                'content' => 'Create a title for this content: ' . self::sanitize($content),
            ],
        ];
    }

    /**
     * Build description generation prompt
     */
    public static function descriptionPrompt(string $content): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'You are an SEO expert specializing in meta descriptions. '
                    . 'Generate a compelling meta description (150-160 characters) that includes relevant keywords and entices clicks. '
                    . 'Do not include quotes or extra text, only the description.',
            ],
            [
                'role' => 'user',
                'content' => 'Create an SEO description for this content: ' . self::sanitize($content),
            ],
        ];
    }

    /**
     * Build tags generation prompt
     */
    public static function tagsPrompt(string $content): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'You are a content strategist specializing in tag generation. '
                    . 'Generate 8-10 relevant, single-word or hyphenated tags for the content. '
                    . 'Return ONLY comma-separated tags without any other text.',
            ],
            [
                'role' => 'user',
                'content' => 'Generate tags for this content: ' . self::sanitize($content),
            ],
        ];
    }

    /**
     * Build chapters/timestamps generation prompt
     */
    public static function chaptersPrompt(string $transcript): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'You are a video editor specializing in creating chapter breakdowns from transcripts. '
                    . 'Analyze the provided transcript and generate 5-10 chapter timestamps and titles. '
                    . 'Format your response EXACTLY as: [00:00:00] Chapter Title' . "\n" . '[00:01:30] Next Chapter' . "\n"
                    . 'Use format HH:MM:SS for timestamps. Only include the formatted chapters, no other text.',
            ],
            [
                'role' => 'user',
                'content' => 'Create chapters for this transcript: ' . self::sanitize($transcript),
            ],
        ];
    }

    /**
     * Build short-form ideas generation prompt for different platforms
     */
    public static function shortsPrompt(string $content, string $platform): array
    {
        $platformInstructions = match ($platform) {
            'tiktok' => 'TikTok (15-60 seconds, trendy, engaging, viral-worthy)',
            'reels' => 'Instagram Reels (15-90 seconds, visually focused, music-friendly)',
            'shorts' => 'YouTube Shorts (15-60 seconds, educational or entertaining)',
            default => 'short-form video',
        };

        return [
            [
                'role' => 'system',
                'content' => 'You are a social media content strategist specializing in short-form video ideas. '
                    . 'Generate 5-8 innovative, platform-optimized video ideas for ' . $platformInstructions . '. '
                    . 'Each idea should be 1-2 sentences describing the concept, hook, and key elements. '
                    . 'Return ONLY the numbered list of ideas, no extra text.',
            ],
            [
                'role' => 'user',
                'content' => 'Generate short-form video ideas for ' . $platform . ' based on this content: ' . self::sanitize($content),
            ],
        ];
    }

    /**
     * Sanitize user input for safe injection into prompts
     */
    private static function sanitize(string $input): string
    {
        // Remove any potentially harmful characters
        $input = trim($input);

        // Limit length to prevent token overflow
        $maxLength = 2000;
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength) . '...';
        }

        // Remove line breaks and excessive whitespace
        $input = preg_replace('/\s+/', ' ', $input) ?? $input;

        return $input;
    }
}
