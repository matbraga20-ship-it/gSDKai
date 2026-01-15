<?php

namespace OpenAI\DTO;

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
