<?php
require_once __DIR__ . '/../bootstrap.php';

use GuzzleHttp\Client;
use OpenAI\Support\Config;

// Enable mock mode for OpenAI calls during tests — save previous value to restore later
$prevMock = Config::get('openai_mock', false);
Config::set('openai_mock', true);
Config::save();

// Simple test runner for playground endpoints
// Requires dev server running at http://localhost:8000

$base = 'http://localhost:8000';
$client = new Client(['base_uri' => $base, 'timeout' => 60]);

function ok($msg) { echo "[OK] " . $msg . PHP_EOL; }
function err($msg) { echo "[ERR] " . $msg . PHP_EOL; }

echo "Playground test runner\n";

$tests = [];

$tests[] = function() use ($client) {
    echo "\n- Testing Title Generation...\n";
    $res = $client->post('/api/generate/title', [
        'json' => ['content' => 'This is a test article about PHP SDKs and OpenAI integration.']
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['result'])) {
        ok('Title generated: ' . substr($body['data']['result'], 0, 80));
    } else {
        err('Title generation failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Description Generation...\n";
    $res = $client->post('/api/generate/description', [
        'json' => ['content' => 'Short summary of a video about AI SDKs and best practices.']
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['result'])) {
        ok('Description generated: ' . substr($body['data']['result'], 0, 120));
    } else {
        err('Description generation failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Tags Generation...\n";
    $res = $client->post('/api/generate/tags', [
        'json' => ['content' => 'AI, PHP SDK, OpenAI, integration, tutorial']
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['result'])) {
        ok('Tags: ' . substr($body['data']['result'], 0, 120));
    } else {
        err('Tags generation failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Chapters/Timestamps Generation...\n";
    $transcript = "00:00 Intro\n00:15 Overview of SDK\n01:00 Demo and testing\n";
    $res = $client->post('/api/generate/timestamps', [
        'json' => ['transcript' => $transcript]
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['chapters'])) {
        ok('Chapters generated: ' . count($body['data']['chapters']));
    } else {
        err('Chapters generation failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Shorts Ideas Generation...\n";
    $res = $client->post('/api/generate/shorts-ideas', [
        'json' => ['content' => 'How to build a PHP SDK for OpenAI', 'platform' => 'tiktok']
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['ideas'])) {
        ok('Shorts ideas count: ' . count($body['data']['ideas']));
    } else {
        err('Shorts ideas failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Embeddings...\n";
    $res = $client->post('/api/embeddings', [
        'json' => ['input' => 'Test embedding for PHP SDK']
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['embeddings'])) {
        ok('Embeddings returned');
    } else {
        err('Embeddings failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Image Generation...\n";
    $res = $client->post('/api/images/generate', [
        'json' => ['prompt' => 'A minimalist vector illustration of a robot coding in PHP', 'options' => ['size' => '512x512', 'n' => 1]]
    ]);
    $body = json_decode((string)$res->getBody(), true);
    if (!empty($body['success']) && !empty($body['data']['images'])) {
        ok('Image generation returned');
    } else {
        err('Image generation failed: ' . json_encode($body));
    }
};

$tests[] = function() use ($client) {
    echo "\n- Testing Audio Transcription (creating silent WAV)...\n";
    $wav = __DIR__ . '/sample_silence.wav';
    // Create 1s silence 16-bit PCM 16000Hz mono
    $sr = 16000; $secs = 1; $n = $sr * $secs; $bits = 16; $channels = 1; $byteRate = $sr * $channels * ($bits/8);
    $data = str_repeat(pack('v', 0), $n * $channels); // silence
    $riff = 'RIFF';
    $wave = 'WAVE';
    $fmt = 'fmt ';
    $dataChunk = 'data';
    $fmtChunkData = pack('VvvVVv', 16, 1, $channels, $sr, $byteRate, ($channels * ($bits/8)), $bits);
    $fileData = $riff . pack('V', 36 + strlen($data)) . $wave . $fmt . $fmtChunkData . $dataChunk . pack('V', strlen($data)) . $data;
    file_put_contents($wav, $fileData);

    try {
        $res = $client->request('POST', '/api/audio/transcribe', [
            'multipart' => [
                ['name' => 'file', 'contents' => fopen($wav, 'r'), 'filename' => basename($wav)],
            ],
            'headers' => []
        ]);
        $body = json_decode((string)$res->getBody(), true);
        if (!empty($body['success']) && !empty($body['data']['transcription'])) {
            ok('Audio transcription returned');
        } else {
            err('Audio transcription response: ' . json_encode($body));
        }
    } catch (Exception $e) {
        err('Audio transcription request failed: ' . $e->getMessage());
    }
};

// Run tests sequentially and ensure we restore mock setting afterwards
try {
    foreach ($tests as $t) {
        try {
            $t();
        } catch (Exception $e) {
            err('Test threw exception: ' . $e->getMessage());
        }
    }
} finally {
    // Disable mock mode after tests (revert to false)
    Config::set('openai_mock', false);
    Config::save();

    echo "\nDone. openai_mock set to false\n";
}
