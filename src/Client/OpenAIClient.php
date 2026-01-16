<?php

namespace OpenAI\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\Config;
use OpenAI\Support\LoggerService;

/**
 * OpenAI API Client
 * Handles communication with OpenAI Responses API with retries and error handling
 */
class OpenAIClient
{
    private Client $httpClient;
    private const OPENAI_API_URL = 'https://api.openai.com/v1';
    private const DEFAULT_ENDPOINT = '/responses';
    private const MAX_RETRIES = 3;

    // Retry delays in seconds (exponential backoff)
    private const RETRY_DELAYS = [1, 2, 4];

    public function __construct()
    {
        $timeout = (int) Config::get('openai_timeout', 30);

        $options = [
            'timeout' => $timeout,
            'connect_timeout' => $timeout,
        ];

        // If a bundled CA bundle exists in the project, use it for Guzzle verification
        $projectRoot = dirname(__DIR__, 2);
        $bundledCert = $projectRoot . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR . 'cacert.pem';
        if (is_file($bundledCert)) {
            $options['verify'] = $bundledCert;
        }

        $this->httpClient = new Client($options);
    }

    /**
     * Create a Responses API call (convenience wrapper)
     */
    public function responsesCreate(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        // Mock support for tests
        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return [
                'id' => 'resp_mock_1',
                'model' => $payload['model'] ?? 'mock-model',
                'output_text' => 'This is a mocked response for testing.',
                'output' => [
                    [
                        'id' => 'msg_mock_1',
                        'type' => 'message',
                        'content' => [
                            ['type' => 'output_text', 'text' => 'This is a mocked response for testing.']
                        ]
                    ]
                ],
                'usage' => ['input_tokens' => 1, 'output_tokens' => 4, 'total_tokens' => 5],
            ];
        }

        return $this->requestWithRetry($apiKey, $payload, '/responses');
    }

    /**
     * Create Embeddings
     */
    public function embeddingsCreate(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return [
                'object' => 'list',
                'data' => [
                    [
                        'object' => 'embedding',
                        'embedding' => array_fill(0, 8, 0.123456),
                        'index' => 0,
                    ],
                ],
                'model' => $payload['model'] ?? 'mock-embedding-model',
            ];
        }

        return $this->requestWithRetry($apiKey, $payload, '/embeddings');
    }

    /**
     * List available models
     */
    public function modelsList(): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return [
                'data' => [
                    ['id' => 'mock-model-1', 'object' => 'model'],
                ],
            ];
        }

        return $this->requestWithRetry($apiKey, [], '/models', 'GET');
    }

    /**
     * Create Images (image generation)
     */
    public function imagesCreate(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        // Mock support
        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return [
                'created' => time(),
                'data' => [
                    ['b64_json' => base64_encode('mock-image-bytes')]
                ],
            ];
        }

        // Some OpenAI deployments use /images/generations
        // Some OpenAI deployments use /images/generations
        return $this->requestWithRetry($apiKey, $payload, '/images/generations');
    }

    /**
     * List files
     */
    public function filesList(): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return ['data' => [['id' => 'file_mock_1', 'filename' => 'mock.txt']]];
        }

        return $this->requestWithRetry($apiKey, [], '/files', 'GET');
    }

    /**
     * Upload a file (multipart)
     */
    public function filesUpload(string $filePath, array $fields = []): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (!is_file($filePath)) {
            throw new OpenAIException('File not found: ' . $filePath);
        }

        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return ['id' => 'file_mock_1', 'filename' => basename($filePath)];
        }

        $url = rtrim(self::OPENAI_API_URL, '/') . '/files';
        $multipart = [[
            'name' => 'file',
            'contents' => fopen($filePath, 'r'),
            'filename' => basename($filePath),
        ]];

        foreach ($fields as $k => $v) {
            $multipart[] = ['name' => $k, 'contents' => is_array($v) ? json_encode($v) : (string)$v];
        }

        try {
            $response = $this->httpClient->post($url, [
                'headers' => ['Authorization' => 'Bearer ' . $apiKey],
                'multipart' => $multipart,
            ]);
        } catch (RequestException $e) {
            throw $this->handleOpenAIException($e);
        }

        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new OpenAIException('Invalid JSON response from OpenAI API');
        }

        return $data;
    }

    /**
     * Transcribe audio (speech-to-text)
     */
    public function audioTranscribe(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        // Mock support
        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return [
                'id' => 'tr_mock_1',
                'text' => 'This is a mocked transcription result.',
            ];
        }

        // The audio transcription endpoint expects multipart/form-data with a file upload
        // If caller provided 'file_path', send as multipart, otherwise fallback to JSON
        if (!empty($payload['file_path']) && is_file($payload['file_path'])) {
            $url = rtrim(self::OPENAI_API_URL, '/') . '/audio/transcriptions';

            $multipart = [
                [
                    'name' => 'file',
                    'contents' => fopen($payload['file_path'], 'r'),
                ],
            ];

            // Include other fields as form params
            foreach ($payload as $key => $value) {
                if ($key === 'file_path') continue;
                $multipart[] = [
                    'name' => $key,
                    'contents' => is_array($value) ? json_encode($value) : (string)$value,
                ];
            }

            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => $multipart,
            ];

            try {
                $response = $this->httpClient->post($url, $options);
            } catch (RequestException $e) {
                throw $this->handleOpenAIException($e);
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode >= 300) {
                throw new OpenAIException("HTTP {$statusCode} response from OpenAI API");
            }

            $body = (string)$response->getBody();
            $data = json_decode($body, true);

            if (!is_array($data)) {
                throw new OpenAIException('Invalid JSON response from OpenAI API');
            }

            return $data;
        }

        return $this->requestWithRetry($apiKey, $payload, '/audio/transcriptions');
    }

    /**
     * Create a moderation request
     */
    public function moderationCreate(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (Config::get('openai_mock', false) || getenv('OPENAI_MOCK')) {
            return ['id' => 'mod_mock_1', 'results' => [['flagged' => false]]];
        }

        return $this->requestWithRetry($apiKey, $payload, '/moderations');
    }

    /**
     * Make a request to the OpenAI Responses API
     *
     * @param array $payload The request payload (system, user messages, model, etc.)
     * @return array The decoded response from OpenAI
     * @throws OpenAIException
     */
    public function generateResponse(array $payload): array
    {
        $apiKey = Config::get('openai_api_key');

        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        LoggerService::debug('OpenAI API Request', [
            'model' => $payload['model'] ?? 'unknown',
            'endpoint' => self::OPENAI_API_URL . self::DEFAULT_ENDPOINT,
        ]);

        return $this->requestWithRetry($apiKey, $payload, self::DEFAULT_ENDPOINT);
    }

    /**
     * Make request with exponential backoff retry logic
     */
    private function requestWithRetry(string $apiKey, array $payload, string $endpoint = self::DEFAULT_ENDPOINT, string $method = 'POST'): array
    {
        $lastException = null;

        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            try {
                return $this->makeRequest($apiKey, $payload, $endpoint, $method);
            } catch (RequestException $e) {
                $lastException = $e;

                // Check if it's a retryable error
                if (!$this->isRetryable($e)) {
                    throw $this->handleOpenAIException($e);
                }

                // Don't retry on last attempt
                if ($attempt < self::MAX_RETRIES - 1) {
                    $delay = self::RETRY_DELAYS[$attempt] ?? 4;

                    LoggerService::warning("OpenAI request failed, retrying in {$delay}s", [
                        'attempt' => $attempt + 1,
                        'error' => $e->getMessage(),
                    ]);

                    sleep($delay);
                    continue;
                }
            } catch (\Exception $e) {
                throw new OpenAIException('Unexpected error: ' . $e->getMessage());
            }
        }

        // All retries exhausted
        throw $this->handleOpenAIException($lastException);
    }

    /**
     * Make a generic API request (advanced usage).
     */
    public function request(string $endpoint, array $payload = [], string $method = 'POST'): array
    {
        $apiKey = Config::get('openai_api_key');

        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        return $this->requestWithRetry($apiKey, $payload, $endpoint, $method);
    }

    /**
     * Make the actual HTTP request
     */
    private function makeRequest(string $apiKey, array $payload, string $endpoint = self::DEFAULT_ENDPOINT, string $method = 'POST'): array
    {
        $url = rtrim(self::OPENAI_API_URL, '/') . '/' . ltrim($endpoint, '/');

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ];

        $options = ['headers' => $headers];

        $httpMethod = strtoupper($method);

        // Use query string for GET requests
        if ($httpMethod === 'GET' && !empty($payload)) {
            $options['query'] = $payload;
        }

        if (in_array($httpMethod, ['POST', 'PUT', 'PATCH'], true)) {
            $options['json'] = $payload;
        }

        $response = match ($httpMethod) {
            'GET' => $this->httpClient->get($url, $options),
            'POST' => $this->httpClient->post($url, $options),
            'PUT' => $this->httpClient->put($url, $options),
            'PATCH' => $this->httpClient->patch($url, $options),
            'DELETE' => $this->httpClient->delete($url, $options),
            default => throw new OpenAIException('Unsupported HTTP method: ' . $httpMethod),
        };

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new OpenAIException("HTTP {$statusCode} response from OpenAI API");
        }

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new OpenAIException('Invalid JSON response from OpenAI API');
        }

        LoggerService::debug('OpenAI API Success', [
            'model' => $payload['model'] ?? 'unknown',
            'status' => $statusCode,
        ]);

        return $data;
    }

    /**
     * Check if error is retryable (transient)
     */
    private function isRetryable(RequestException $e): bool
    {
        // Connection errors are retryable
        if ($e instanceof ConnectException) {
            return true;
        }

        // Check HTTP status codes
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();

            // Retry on server errors (5xx) and rate limit (429)
            return in_array($statusCode, [429, 500, 502, 503, 504]);
        }

        return false;
    }

    /**
     * Handle and convert OpenAI exceptions to our custom exception
     */
    private function handleOpenAIException(\Throwable $e): OpenAIException
    {
        $message = $e->getMessage();
        $code = 'OPENAI_ERROR';
        $data = null;

        if ($e instanceof ClientException && $e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            // Try to parse error from OpenAI
            $errorData = json_decode($body, true);

            if (is_array($errorData) && isset($errorData['error'])) {
                $error = $errorData['error'];
                if (is_array($error)) {
                    $message = $error['message'] ?? $message;
                    $code = $error['code'] ?? $code;
                    $data = $error;
                }
            }

            // Map HTTP status codes to friendly messages
            switch ($statusCode) {
                case 401:
                    $message = 'Invalid OpenAI API key. Please check your configuration.';
                    $code = 'INVALID_API_KEY';
                    break;
                case 429:
                    $message = 'OpenAI rate limit exceeded. Please try again later.';
                    $code = 'RATE_LIMIT';
                    break;
                case 500:
                case 502:
                case 503:
                case 504:
                    $message = 'OpenAI service temporarily unavailable. Please try again.';
                    $code = 'SERVICE_UNAVAILABLE';
                    break;
            }

            LoggerService::error('OpenAI API Error', [
                'status_code' => $statusCode,
                'error_code' => $code,
                'message' => $message,
            ]);
        } else {
            LoggerService::error('OpenAI Request Exception', [
                'error' => get_class($e),
                'message' => $message,
            ]);
        }

        return new OpenAIException($message, 0, $code, $data);
    }

    /**
     * Test API connectivity with a simple request
     */
    public function testConnection(): bool
    {
        try {
            // Simple test request using Responses API (input)
            $payload = [
                'model' => Config::get('openai_model', 'gpt-5.2'),
                'input' => 'Test',
            ];

            $response = $this->generateResponse($payload);

            // Responses API returns 'output' or 'output_text' depending on SDK; check generic shapes
            if (isset($response['output_text'])) {
                return !empty(trim((string)$response['output_text']));
            }

            if (isset($response['output']) && is_array($response['output']) && count($response['output']) > 0) {
                return true;
            }

            // Fallback: some legacy responses include 'choices'
            return isset($response['choices']) && is_array($response['choices']) && count($response['choices']) > 0;
        } catch (\Exception $e) {
            LoggerService::error('API Connection Test Failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
