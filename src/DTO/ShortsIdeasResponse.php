<?php

namespace OpenAI\DTO;

/**
 * Shorts Ideas Response DTO
 */
class ShortsIdeasResponse
{
    /** @var string[] */
    private array $ideas;

    /** @param string[] $ideas */
    public function __construct(
        array $ideas,
        public readonly string $platform,
        public readonly ?string $model = null,
        public readonly ?int $usage = null,
    ) {
        $this->ideas = $ideas;
    }

    /** @return string[] */
    public function getIdeas(): array
    {
        return $this->ideas;
    }

    public function toArray(): array
    {
        return [
            'ideas' => $this->ideas,
            'platform' => $this->platform,
            'model' => $this->model,
            'usage' => $this->usage,
        ];
    }
}
