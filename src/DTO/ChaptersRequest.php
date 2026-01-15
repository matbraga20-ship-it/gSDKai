<?php

namespace OpenAI\DTO;

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
