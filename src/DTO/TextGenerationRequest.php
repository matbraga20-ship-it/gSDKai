<?php

namespace OpenAI\DTO;

class TextGenerationRequest
{
    public function __construct(
        public readonly string $content,
        public readonly string $type = 'title',
        public readonly ?string $language = 'en',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'] ?? '',
            type: $data['type'] ?? 'title',
            language: $data['language'] ?? 'en',
        );
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'type' => $this->type,
            'language' => $this->language,
        ];
    }
}
