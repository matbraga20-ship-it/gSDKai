<?php

namespace OpenAI\DTO;

/**
 * Chapter/Timestamp Request DTO
 */
class ChaptersRequest
{
    public function __construct(
        public readonly string $transcript,
        public readonly ?string $language = 'en',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            transcript: $data['transcript'] ?? '',
            language: $data['language'] ?? 'en',
        );
    }

    public function toArray(): array
    {
        return [
            'transcript' => $this->transcript,
            'language' => $this->language,
        ];
    }
}

/**
 * Chapter item
 */
class Chapter
{
    public function __construct(
        public readonly string $timestamp,
        public readonly string $title,
    ) {
    }

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'title' => $this->title,
        ];
    }
}

/**
 * Chapters Response DTO
 */
class ChaptersResponse
{
    /** @var Chapter[] */
    private array $chapters;

    /** @param Chapter[] $chapters */
    public function __construct(
        array $chapters,
        public readonly ?string $model = null,
        public readonly ?int $usage = null,
    ) {
        $this->chapters = $chapters;
    }

    /** @return Chapter[] */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    public function toArray(): array
    {
        return [
            'chapters' => array_map(fn(Chapter $c) => $c->toArray(), $this->chapters),
            'model' => $this->model,
            'usage' => $this->usage,
        ];
    }
}
