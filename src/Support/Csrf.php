<?php

namespace OpenAI\Support;

/**
 * CSRF Token Protection
 * Generates and validates CSRF tokens for form submission
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Generate or retrieve CSRF token
     */
    public static function token(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = self::generateToken();
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Generate a new random token
     */
    private static function generateToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH / 2));
    }

    /**
     * Validate CSRF token from request
     */
    public static function verify(string $token): bool
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Validate CSRF token from POST/PUT/DELETE request
     */
    public static function verifyRequest(): bool
    {
        // Get token from POST, JSON body, or header
        $token = null;

        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
        } elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } else {
            // Try to get from JSON body
            $json = file_get_contents('php://input');
            if (!empty($json)) {
                $data = json_decode($json, true);
                if (is_array($data) && isset($data['_csrf_token'])) {
                    $token = $data['_csrf_token'];
                }
            }
        }

        if ($token === null) {
            return false;
        }

        return self::verify($token);
    }

    /**
     * Generate hidden input field for forms
     */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::token()) . '">';
    }
}
