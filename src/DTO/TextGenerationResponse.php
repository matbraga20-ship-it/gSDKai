<?php

namespace OpenAI\DTO;

class TextGenerationResponse
{
    public function __construct(
        public readonly string $result,
        public readonly string $type,
        public readonly ?string $model = null,
        public readonly ?int $usage = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'result' => $this->result,
            'type' => $this->type,
            'model' => $this->model,
            'usage' => $this->usage,
        ];
    }
}
