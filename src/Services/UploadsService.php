<?php

namespace OpenAI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Support\Config;

/**
 * Uploads Service
 * Handles multipart upload lifecycle for large files.
 */
class UploadsService
{
    private Client $httpClient;
    private const OPENAI_API_URL = 'https://api.openai.com/v1';

    public function __construct()
    {
        $timeout = (int) Config::get('openai_timeout', 30);
        $options = [
            'timeout' => $timeout,
            'connect_timeout' => $timeout,
        ];

        $projectRoot = dirname(__DIR__, 2);
        $bundledCert = $projectRoot . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR . 'cacert.pem';
        if (is_file($bundledCert)) {
            $options['verify'] = $bundledCert;
        }

        $this->httpClient = new Client($options);
    }

    /**
     * Create an upload session.
     */
    public function create(array $payload): array
    {
        return $this->request('/uploads', $payload, 'POST');
    }

    /**
     * Upload a file part using multipart/form-data.
     */
    public function addPart(string $uploadId, string $filePath): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        if (!is_file($filePath)) {
            throw new OpenAIException('File not found: ' . $filePath);
        }

        $url = rtrim(self::OPENAI_API_URL, '/') . '/uploads/' . $uploadId . '/parts';

        try {
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ],
                ],
            ]);
        } catch (RequestException $e) {
            throw new OpenAIException('Upload part failed: ' . $e->getMessage());
        }

        $data = json_decode((string)$response->getBody(), true);
        if (!is_array($data)) {
            throw new OpenAIException('Invalid JSON response from OpenAI API');
        }

        return $data;
    }

    /**
     * Complete an upload session.
     */
    public function complete(string $uploadId, array $payload): array
    {
        return $this->request('/uploads/' . $uploadId . '/complete', $payload, 'POST');
    }

    /**
     * Retrieve an upload session.
     */
    public function retrieve(string $uploadId): array
    {
        return $this->request('/uploads/' . $uploadId, [], 'GET');
    }

    private function request(string $endpoint, array $payload, string $method): array
    {
        $apiKey = Config::get('openai_api_key');
        if (empty($apiKey)) {
            throw new OpenAIException('OpenAI API key is not configured');
        }

        $url = rtrim(self::OPENAI_API_URL, '/') . '/' . ltrim($endpoint, '/');

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
        ];

        $httpMethod = strtoupper($method);
        if ($httpMethod === 'GET' && !empty($payload)) {
            $options['query'] = $payload;
        } elseif (!empty($payload)) {
            $options['json'] = $payload;
        }

        try {
            $response = match ($httpMethod) {
                'GET' => $this->httpClient->get($url, $options),
                'POST' => $this->httpClient->post($url, $options),
                default => throw new OpenAIException('Unsupported HTTP method: ' . $httpMethod),
            };
        } catch (RequestException $e) {
            throw new OpenAIException('OpenAI request failed: ' . $e->getMessage());
        }

        $data = json_decode((string)$response->getBody(), true);
        if (!is_array($data)) {
            throw new OpenAIException('Invalid JSON response from OpenAI API');
        }

        return $data;
    }
}
