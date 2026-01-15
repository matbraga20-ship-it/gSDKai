<?php

namespace OpenAI\Exceptions;

use Exception;

/**
 * OpenAI API Exception
 * Thrown when OpenAI API requests fail
 */
class OpenAIException extends Exception
{
    private ?string $apiErrorCode = null;
    private ?array $apiErrorData = null;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?string $apiErrorCode = null,
        ?array $apiErrorData = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->apiErrorCode = $apiErrorCode;
        $this->apiErrorData = $apiErrorData;
    }

    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    public function getApiErrorData(): ?array
    {
        return $this->apiErrorData;
    }
}
