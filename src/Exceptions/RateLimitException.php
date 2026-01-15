<?php

namespace OpenAI\Exceptions;

use Exception;

/**
 * Rate Limit Exception
 * Thrown when rate limit is exceeded
 */
class RateLimitException extends Exception
{
    private int $retryAfter = 60;

    public function __construct(
        string $message = 'Rate limit exceeded',
        int $retryAfter = 60
    ) {
        parent::__construct($message);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
