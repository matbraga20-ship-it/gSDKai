<?php

namespace OpenAI\DTO;

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
