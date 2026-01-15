<?php

namespace OpenAI\DTO;

/**
 * Shorts Ideas Request DTO
 */
class ShortsIdeasRequest
{
    public function __construct(
        public readonly string $content,
        public readonly string $platform = 'tiktok', // tiktok, reels, shorts
        public readonly ?string $language = 'en',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'] ?? '',
            platform: $data['platform'] ?? 'tiktok',
            language: $data['language'] ?? 'en',
        );
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'platform' => $this->platform,
            'language' => $this->language,
        ];
    }
}
