# PHP SDK Reference

This guide covers using the OpenAI Content Toolkit as a PHP library in your own applications.

## Installation

Add to your `composer.json`:

```json
{
  "require": {
    "openai/content-toolkit-sdk": "^1.0"
  }
}
```

Or install directly:

```bash
composer require openai/content-toolkit-sdk
```

## Initialization

```php
<?php
require 'vendor/autoload.php';

use OpenAI\Support\Config;

// Load configuration from storage/app/config.json
Config::load();
```

## Configuration

### Setting Configuration Values

```php
use OpenAI\Support\Config;

// Set API key
Config::set('openai_api_key', 'sk-...');

// Set model
Config::set('openai_model', 'gpt-4o-mini');

// Set temperature (0-2)
Config::set('openai_temperature', 0.7);

// Set max output tokens
Config::set('openai_max_output_tokens', 800);

// Set timeout (seconds)
Config::set('openai_timeout', 30);

// Save to storage/app/config.json
Config::save();
```

### Reading Configuration

```php
// Get single value with default
$apiKey = Config::get('openai_api_key');
$model = Config::get('openai_model', 'gpt-4o-mini');

// Get all configuration
$all = Config::all();

// Check if API key is configured
if (Config::hasApiKey()) {
    echo "API key is configured";
}

// Get masked API key (first 10 chars visible)
echo Config::getApiKeyMasked();  // sk-...

// Validate configuration
$errors = Config::validate();
if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo "$field: $error\n";
    }
}
```

## Services

### TextService

Generates titles, descriptions, and tags from content.

```php
<?php
use OpenAI\Services\TextService;

$service = new TextService();

// Generate Title (40-60 characters, SEO-optimized)
$titleResponse = $service->generateTitle('Your content text here...');
echo $titleResponse->result;  // Generated title
echo $titleResponse->model;   // Model used (e.g., "gpt-4o-mini")
echo $titleResponse->usage;   // Tokens used

// Generate Description (150-160 chars, meta-description)
$descResponse = $service->generateDescription('Your content here...');
echo $descResponse->result;

// Generate Tags (comma-separated)
$tagsResponse = $service->generateTags('Your content here...');
echo $tagsResponse->result;  // "tag1, tag2, tag3, ..."
```

### ResponsesService

Send direct payloads to the OpenAI Responses API.

```php
<?php
use OpenAI\Services\ResponsesService;

$service = new ResponsesService();

$response = $service->create([
    'model' => 'gpt-4o-mini',
    'input' => [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant.'
        ],
        [
            'role' => 'user',
            'content' => 'Summarize this text in one sentence.'
        ]
    ],
    'max_output_tokens' => 120,
    'temperature' => 0.7
]);

echo $response['output_text'] ?? '';
```

### ChaptersService

Generates chapter breakdowns from video transcripts.

```php
<?php
use OpenAI\Services\ChaptersService;

$service = new ChaptersService();

$response = $service->generateChapters('Full video transcript here...');

// Get chapters array
$chapters = $response->getChapters();

// Iterate through chapters
foreach ($chapters as $chapter) {
    echo "[{$chapter->timestamp}] {$chapter->title}\n";
    // Output:
    // [00:00:00] Introduction
    // [00:01:30] Main Topic
    // [00:05:45] Conclusion
}

// Access metadata
echo $response->model;  // Model used
echo $response->usage;  // Total tokens
```

### ShortsIdeasService

Generates platform-specific short-form video ideas.

```php
<?php
use OpenAI\Services\ShortsIdeasService;

$service = new ShortsIdeasService();

// Generate TikTok ideas
$response = $service->generateIdeas('Your content here', 'tiktok');

// Generate Instagram Reels ideas
$response = $service->generateIdeas('Your content here', 'reels');

// Generate YouTube Shorts ideas
$response = $service->generateIdeas('Your content here', 'shorts');

// Get ideas array
$ideas = $response->getIdeas();

foreach ($ideas as $idea) {
    echo "- $idea\n";
}

// Access metadata
echo $response->platform;  // 'tiktok', 'reels', or 'shorts'
echo $response->model;
echo $response->usage;
```

### EmbeddingsService

Generate embeddings for semantic search and retrieval.

```php
<?php
use OpenAI\Services\EmbeddingsService;

$service = new EmbeddingsService();

$embeddings = $service->create('Sample text to embed');

// Raw embeddings array (API response)
print_r($embeddings);
```

### ImagesService

Generate images from a text prompt.

```php
<?php
use OpenAI\Services\ImagesService;

$service = new ImagesService();

$images = $service->generate('A futuristic city at sunset', [
    'size' => '1024x1024',
    'n' => 1,
]);

// Raw images payload (base64 or URLs depending on API)
print_r($images);
```

### AudioService

Transcribe audio files to text.

```php
<?php
use OpenAI\Services\AudioService;

$service = new AudioService();

$result = $service->transcribe('/path/to/audio.mp3', [
    'language' => 'en',
]);

print_r($result);
```

### ModerationService

Moderate text against OpenAI policies.

```php
<?php
use OpenAI\Services\ModerationService;

$service = new ModerationService();

$result = $service->moderate([
    'This is a sample text to moderate.'
]);

print_r($result);
```

### ModelsService

List available models.

```php
<?php
use OpenAI\Services\ModelsService;

$service = new ModelsService();

$models = $service->list();

print_r($models);
```

### FilesService

List and upload files.

```php
<?php
use OpenAI\Services\FilesService;

$service = new FilesService();

// List files
$files = $service->list();
print_r($files);

// Upload file (optional purpose field)
$uploaded = $service->upload('/path/to/data.jsonl', [
    'purpose' => 'fine-tune'
]);

print_r($uploaded);
```

### AssistantsService

Manage assistants (create, list, retrieve, update, delete).

```php
<?php
use OpenAI\Services\AssistantsService;

$service = new AssistantsService();

// Create assistant
$assistant = $service->create([
    'name' => 'Content Helper',
    'model' => 'gpt-4o-mini',
    'instructions' => 'You are a helpful writing assistant.'
]);

// List assistants
$list = $service->list();

// Retrieve assistant
$retrieved = $service->retrieve($assistant['id']);

// Update assistant
$updated = $service->update($assistant['id'], [
    'instructions' => 'Updated instructions.'
]);

// Delete assistant
$deleted = $service->delete($assistant['id']);
```

### ThreadsService

Manage threads for Assistants.

```php
<?php
use OpenAI\Services\ThreadsService;

$service = new ThreadsService();

// Create thread
$thread = $service->create([
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!']
    ]
]);

// Retrieve thread
$retrieved = $service->retrieve($thread['id']);

// Update thread metadata
$updated = $service->update($thread['id'], [
    'metadata' => ['source' => 'sdk']
]);

// Delete thread
$deleted = $service->delete($thread['id']);
```

### MessagesService

Manage messages inside threads.

```php
<?php
use OpenAI\Services\MessagesService;

$service = new MessagesService();

// Create message
$message = $service->create('thread_id', [
    'role' => 'user',
    'content' => 'Draft a product description.'
]);

// List messages
$messages = $service->list('thread_id');

// Retrieve message
$retrieved = $service->retrieve('thread_id', $message['id']);
```

### RunsService

Run assistants on threads and retrieve results.

```php
<?php
use OpenAI\Services\RunsService;

$service = new RunsService();

// Create run
$run = $service->create('thread_id', [
    'assistant_id' => 'assistant_id'
]);

// Retrieve run
$retrieved = $service->retrieve('thread_id', $run['id']);

// List runs
$runs = $service->list('thread_id');

// Cancel run
$cancelled = $service->cancel('thread_id', $run['id']);
```

### VectorStoresService

Create vector stores and manage stored files.

```php
<?php
use OpenAI\Services\VectorStoresService;

$service = new VectorStoresService();

// Create vector store
$store = $service->create([
    'name' => 'Knowledge Base'
]);

// Add file to store
$file = $service->addFile($store['id'], [
    'file_id' => 'file_123'
]);

// List files in store
$files = $service->listFiles($store['id']);
```

### BatchesService

Create and manage batch jobs.

```php
<?php
use OpenAI\Services\BatchesService;

$service = new BatchesService();

$batch = $service->create([
    'input_file_id' => 'file_123',
    'endpoint' => '/v1/responses',
    'completion_window' => '24h'
]);

$list = $service->list();
```

### FineTuningService

Create and manage fine-tuning jobs.

```php
<?php
use OpenAI\Services\FineTuningService;

$service = new FineTuningService();

$job = $service->createJob([
    'training_file' => 'file_123',
    'model' => 'gpt-4o-mini'
]);

$events = $service->listEvents($job['id']);
```

### RealtimeService

Create ephemeral sessions for the Realtime API.

```php
<?php
use OpenAI\Services\RealtimeService;

$service = new RealtimeService();

$session = $service->createSession([
    'model' => 'gpt-4o-realtime-preview',
    'voice' => 'alloy'
]);
```

## OpenAI Client

Direct access to OpenAI API requests (advanced usage). This SDK uses the **Responses API** as the default generation entrypoint.

```php
<?php
use OpenAI\Client\OpenAIClient;

$client = new OpenAIClient();

// Send request to OpenAI Responses API
$response = $client->generateResponse([
    'model' => 'gpt-4o-mini',
    'input' => [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant.'
        ],
        [
            'role' => 'user',
            'content' => 'Hello!'
        ]
    ],
    'max_output_tokens' => 100,
    'temperature' => 0.7
]);

// Access response (Responses API)
echo $response['output_text'] ?? '';
echo $response['usage']['total_tokens'] ?? '';

// Test API connectivity
if ($client->testConnection()) {
    echo "API key is valid and working!";
} else {
    echo "API key test failed";
}
```

### Advanced Generic Requests

For any OpenAI endpoint not covered by a dedicated service, use the generic request method.

```php
<?php
use OpenAI\Client\OpenAIClient;

$client = new OpenAIClient();

// Example: create a vector store
$response = $client->request('/vector_stores', [
    'name' => 'Docs Index'
]);

print_r($response);
```

## API Coverage

This SDK currently covers the following OpenAI endpoints:

- Responses (`/responses`)
- Embeddings (`/embeddings`)
- Models (`/models`)
- Images (`/images/generations`)
- Files (`/files`)
- Audio transcriptions (`/audio/transcriptions`)
- Moderations (`/moderations`)
- Assistants (`/assistants`)
- Threads (`/threads`)
- Messages (`/threads/{thread_id}/messages`)
- Runs (`/threads/{thread_id}/runs`)
- Vector stores (`/vector_stores`)
- Batches (`/batches`)
- Fine-tuning (`/fine_tuning/jobs`)
- Realtime sessions (`/realtime/sessions`)

The SDK also exposes a generic request method for any future endpoints.

## Exception Handling

The SDK throws custom exceptions for various error scenarios:

```php
<?php
use OpenAI\Services\TextService;
use OpenAI\Exceptions\OpenAIException;
use OpenAI\Exceptions\ValidationException;

$service = new TextService();

try {
    $result = $service->generateTitle('Content');
} catch (ValidationException $e) {
    // Handle validation errors (invalid input)
    $errors = $e->getErrors();
    foreach ($errors as $field => $message) {
        echo "$field: $message\n";
    }
} catch (OpenAIException $e) {
    // Handle OpenAI API errors
    echo "API Error: " . $e->getMessage();
    echo "API Code: " . $e->getApiErrorCode();
    // Possible codes: INVALID_API_KEY, RATE_LIMIT, SERVICE_UNAVAILABLE
} catch (Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}
```

## Input Validation

Validate user inputs before processing:

```php
<?php
use OpenAI\Support\Validator;
use OpenAI\Exceptions\ValidationException;

$validator = new Validator();

$validator->required($input, 'name')
    ->minLength($input, 1, 'name')
    ->maxLength($input, 100, 'name')
    ->email($email, 'email_field')
    ->numeric($number, 'age')
    ->inEnum($status, ['active', 'inactive'], 'status');

if (!$validator->passes()) {
    // Get all errors
    $errors = $validator->errors();
    print_r($errors);
} else {
    // Validation passed
    echo "All inputs are valid";
}

// Or throw exception if validation failed
$validator->throwIfFailed();
```

## Logging

Log application events and errors:

```php
<?php
use OpenAI\Support\LoggerService;

// Log different severity levels
LoggerService::debug('Debug message', ['key' => 'value']);
LoggerService::info('Information message');
LoggerService::warning('Warning message');
LoggerService::error('Error message', ['exception' => $e]);
LoggerService::critical('Critical error');

// Logs are written to storage/logs/app.log
```

## Rate Limiting

Check and manage rate limits programmatically:

```php
<?php
use OpenAI\Support\RateLimiter;
use OpenAI\Exceptions\RateLimitException;

try {
    // Check if request is within rate limit
    RateLimiter::check();
    
    // Process request...
    
} catch (RateLimitException $e) {
    echo "Rate limited: " . $e->getMessage();
    echo "Retry after: " . $e->getRetryAfter() . " seconds";
}

// Get current request count
$count = RateLimiter::getCount();  // 0-30

// Get remaining requests
$remaining = RateLimiter::getRemaining();  // 30-0

// Get reset time
$resetTime = RateLimiter::getResetTime();  // Unix timestamp

// Admin: reset rate limit for IP
RateLimiter::reset('192.168.1.1');
```

## CSRF Protection

Generate and validate CSRF tokens for forms:

```php
<?php
use OpenAI\Support\Csrf;

// In form (view/template)
echo Csrf::field();  // Outputs hidden input with token

// OR manually in template
<input type="hidden" name="_csrf_token" value="<?php echo Csrf::token(); ?>">

// Validate on form submission
if (Csrf::verifyRequest()) {
    // Token is valid, process form
} else {
    die('CSRF token validation failed');
}

// Manual validation
$token = $_POST['_csrf_token'] ?? '';
if (Csrf::verify($token)) {
    // Token is valid
}
```

## Response Formatting

Create standardized API responses:

```php
<?php
use OpenAI\Support\ResponseJson;

// Success response
$response = ResponseJson::success([
    'id' => 123,
    'name' => 'Generated Title'
], [
    'generated_at' => date('Y-m-d H:i:s')
]);

// OR send directly
ResponseJson::send($response, 200);

// Error response
$error = ResponseJson::error(
    'Something went wrong',
    'ERROR_CODE',
    null,
    []
);

ResponseJson::send($error, 400);
```

## Complete Example Application

```php
<?php
require 'vendor/autoload.php';

use OpenAI\Support\Config;
use OpenAI\Services\TextService;
use OpenAI\Services\ChaptersService;
use OpenAI\Services\ShortsIdeasService;
use OpenAI\Exceptions\OpenAIException;

// Initialize
Config::load();

if (!Config::hasApiKey()) {
    die('OpenAI API key not configured');
}

// Example 1: Generate title for blog post
try {
    $textService = new TextService();
    $content = "This blog post discusses the benefits of AI in modern software development...";
    $title = $textService->generateTitle($content);
    echo "Blog Title: " . $title->result . "\n";
} catch (OpenAIException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 2: Generate chapters from video transcript
try {
    $chaptersService = new ChaptersService();
    $transcript = "Welcome to our video. First, we'll discuss... [full transcript]...";
    $chapters = $chaptersService->generateChapters($transcript);
    
    foreach ($chapters->getChapters() as $chapter) {
        echo "[{$chapter->timestamp}] {$chapter->title}\n";
    }
} catch (OpenAIException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 3: Generate TikTok ideas
try {
    $shortsService = new ShortsIdeasService();
    $ideas = $shortsService->generateIdeas('AI and productivity', 'tiktok');
    
    echo "TikTok Ideas:\n";
    foreach ($ideas->getIdeas() as $idea) {
        echo "- $idea\n";
    }
} catch (OpenAIException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Data Transfer Objects (DTOs)

DTOs are used for type-safe input/output handling:

```php
<?php
use OpenAI\DTO\TextGenerationRequest;
use OpenAI\DTO\TextGenerationResponse;
use OpenAI\DTO\ChaptersRequest;
use OpenAI\DTO\ChaptersResponse;
use OpenAI\DTO\ShortsIdeasRequest;
use OpenAI\DTO\ShortsIdeasResponse;

// Create request DTO
$request = TextGenerationRequest::fromArray([
    'content' => 'My content',
    'type' => 'title',
    'language' => 'en'
]);

// DTOs can be converted to arrays
$array = $request->toArray();

// Response DTOs provide type-safe access
$response = new TextGenerationResponse(
    result: 'Generated text',
    type: 'title',
    model: 'gpt-4o-mini',
    usage: 45
);

echo $response->result;
```

## Performance Tips

1. **Cache Results**: Cache generated content to avoid repeated API calls
   ```php
   $cacheKey = md5($content);
   if (file_exists("cache/$cacheKey")) {
       return file_get_contents("cache/$cacheKey");
   }
   ```

2. **Batch Processing**: Process multiple items efficiently
   ```php
   foreach ($items as $item) {
       try {
           $result = $service->generateTitle($item);
           // Process result
       } catch (RateLimitException $e) {
           sleep($e->getRetryAfter());
           // Retry
       }
   }
   ```

3. **Monitor API Usage**: Track token consumption
   ```php
   $totalTokens = 0;
   $result = $service->generateTitle($content);
   $totalTokens += $result->usage;  // Track tokens
   ```

4. **Use Appropriate Models**: gpt-4o-mini is fastest for most tasks
   ```php
   Config::set('openai_model', 'gpt-4o-mini');
   ```

## Best Practices

1. **Always handle exceptions** - API calls can fail
2. **Validate user inputs** - Use the Validator class
3. **Log errors** - Use LoggerService for debugging
4. **Store secrets safely** - Never hardcode API keys
5. **Respect rate limits** - Implement backoff strategies
6. **Monitor token usage** - Track API costs
7. **Use CSRF tokens** - Protect forms from attacks
8. **Secure sessions** - Use secure cookie settings

---

For more information, see [README.md](README.md) and [API.md](API.md)
