<?php

namespace OpenAI\Support;

use OpenAI\Exceptions\ValidationException;

/**
 * Input Validator
 * Handles validation of user inputs
 */
class Validator
{
    private array $errors = [];

    /**
     * Validate required field
     */
    public function required(mixed $value, string $field): self
    {
        if (empty($value)) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
        return $this;
    }

    /**
     * Validate string length
     */
    public function minLength(string $value, int $min, string $field): self
    {
        if (strlen($value) < $min) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least $min characters";
        }
        return $this;
    }

    /**
     * Validate maximum string length
     */
    public function maxLength(string $value, int $max, string $field): self
    {
        if (strlen($value) > $max) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed $max characters";
        }
        return $this;
    }

    /**
     * Validate email
     */
    public function email(string $value, string $field): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email';
        }
        return $this;
    }

    /**
     * Validate numeric value within range
     */
    public function numeric(mixed $value, string $field): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be numeric';
        }
        return $this;
    }

    /**
     * Validate in enum
     */
    public function inEnum(mixed $value, array $allowed, string $field): self
    {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' has an invalid value';
        }
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Throw exception if validation failed
     */
    public function throwIfFailed(): void
    {
        if (!$this->passes()) {
            throw new ValidationException(
                'Validation failed: ' . implode(', ', $this->errors),
                $this->errors
            );
        }
    }
}
