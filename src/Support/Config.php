<?php

namespace OpenAI\Support;

use OpenAI\Exceptions\ConfigException;

/**
 * Configuration Manager
 * Handles reading and writing configuration from storage/app/config.json
 */
class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    private const CONFIG_FILE = STORAGE_APP_PATH . '/config.json';

    private const DEFAULT_CONFIG = [
        'openai_api_key' => '',
        'openai_model' => 'gpt-4o-mini',
        'openai_temperature' => 0.7,
        'openai_max_output_tokens' => 800,
        'openai_timeout' => 30,
        'last_error' => null,
        'created_at' => null,
        'updated_at' => null,
    ];

    /**
     * Load configuration from file
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        if (!file_exists(self::CONFIG_FILE)) {
            self::$config = self::DEFAULT_CONFIG;
            self::$config['created_at'] = date('Y-m-d H:i:s');
            self::$config['updated_at'] = date('Y-m-d H:i:s');
            self::save();
        } else {
            $content = file_get_contents(self::CONFIG_FILE);
            $data = json_decode($content, true);

            if (!is_array($data)) {
                throw new ConfigException('Invalid config.json format');
            }

            // Merge with defaults to ensure all keys exist
            self::$config = array_merge(self::DEFAULT_CONFIG, $data);
        }

        self::$loaded = true;
    }

    /**
     * Get a configuration value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        if (array_key_exists($key, self::$config)) {
            return self::$config[$key];
        }

        return $default;
    }

    /**
     * Set a configuration value in memory
     */
    public static function set(string $key, mixed $value): void
    {
        self::load();
        self::$config[$key] = $value;
    }

    /**
     * Save configuration to file
     */
    public static function save(): void
    {
        self::$config['updated_at'] = date('Y-m-d H:i:s');

        $json = json_encode(self::$config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new ConfigException('Failed to encode config to JSON');
        }

        $result = file_put_contents(
            self::CONFIG_FILE,
            $json,
            LOCK_EX
        );

        if ($result === false) {
            throw new ConfigException('Failed to write config file: ' . self::CONFIG_FILE);
        }

        // Ensure file is readable and writable
        chmod(self::CONFIG_FILE, 0600);
    }

    /**
     * Get all configuration
     */
    public static function all(): array
    {
        self::load();
        return self::$config;
    }

    /**
     * Check if API key is configured
     */
    public static function hasApiKey(): bool
    {
        return !empty(self::get('openai_api_key'));
    }

    /**
     * Get API key (for logging purposes, only first 10 chars visible)
     */
    public static function getApiKeyMasked(): string
    {
        $key = self::get('openai_api_key', '');
        if (empty($key)) {
            return 'Not configured';
        }
        return substr($key, 0, 10) . '...' . substr($key, -4);
    }

    /**
     * Record an API error for dashboard display
     */
    public static function recordError(string $message): void
    {
        self::set('last_error', [
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
        self::save();
    }

    /**
     * Validate configuration
     */
    public static function validate(): array
    {
        $errors = [];

        $apiKey = self::get('openai_api_key');
        if (empty($apiKey)) {
            $errors['openai_api_key'] = 'OpenAI API key is required';
        } elseif (strlen($apiKey) < 20) {
            $errors['openai_api_key'] = 'OpenAI API key appears invalid (too short)';
        }

        $model = self::get('openai_model');
        if (empty($model)) {
            $errors['openai_model'] = 'Model must be specified';
        }

        $temp = self::get('openai_temperature');
        if (!is_numeric($temp) || $temp < 0 || $temp > 2) {
            $errors['openai_temperature'] = 'Temperature must be between 0 and 2';
        }

        $maxTokens = self::get('openai_max_output_tokens');
        if (!is_numeric($maxTokens) || $maxTokens < 1 || $maxTokens > 4000) {
            $errors['openai_max_output_tokens'] = 'Max output tokens must be between 1 and 4000';
        }

        $timeout = self::get('openai_timeout');
        if (!is_numeric($timeout) || $timeout < 5 || $timeout > 120) {
            $errors['openai_timeout'] = 'Timeout must be between 5 and 120 seconds';
        }

        return $errors;
    }
}
