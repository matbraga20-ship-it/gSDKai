<?php

require __DIR__ . '/../bootstrap.php';

// Quick test for OpenAI API connectivity
$client = new OpenAI\Client\OpenAIClient();
// Try a direct generateResponse call to surface errors
$payload = [
    'model' => OpenAI\Support\Config::get('openai_model', 'gpt-5.2'),
    'input' => 'Test',
];

try {
    $response = $client->generateResponse($payload);
    echo "generateResponse returned:\n";
    var_dump($response);
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    if (method_exists($e, 'getCode')) {
        echo "Code: " . $e->getCode() . PHP_EOL;
    }
    echo "Trace:\n" . $e->getTraceAsString() . PHP_EOL;
}
