# OpenAI Content Toolkit SDK (PHP)

Commercial-ready PHP SDK and demo panel for OpenAI content workflows using the **Responses API**. Includes production-friendly services for titles, descriptions, tags, chapters, shorts ideas, embeddings, images, moderation, audio transcription, and file operations.

## Highlights

- **Resilient HTTP client** with retries and exponential backoff
- **Ready-to-use services** (Text, Chapters, Shorts, Embeddings, Images, Audio, Files, Moderation, Models)
- **Typed DTOs** for consistent input/output handling
- **Built-in validation, logging, and rate limiting**
- **Mock support** for local testing
- **Playground demo** that mirrors SDK features

## Requirements

- PHP 8.2+
- Composer
- OpenAI API key

## Installation

```bash
composer require openai/content-toolkit-sdk
```

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use OpenAI\Support\Config;
use OpenAI\Services\TextService;

Config::load();
Config::set('openai_api_key', 'sk-...');
Config::set('openai_model', 'gpt-4o-mini');
Config::save();

$service = new TextService();
$response = $service->generateTitle('Your article content...');

echo $response->result;
```

## Playground Demo

The admin panel includes a **Playground** that exposes all SDK features through a friendly UI. Configure your API key in **Settings** and explore every endpoint visually.

## SDK Coverage

Currently supported OpenAI endpoints:

- **Responses** (`/responses`) for text generation
- **Embeddings** (`/embeddings`)
- **Models** (`/models`)
- **Images** (`/images/generations`)
- **Files** (`/files`)
- **Audio** (`/audio/transcriptions`)
- **Moderations** (`/moderations`)

## Documentation

- [SDK Guide](docs/SDK.md)
- [REST API (Admin Panel)](docs/API.md)
- [Installation](docs/INSTALL.md)

## Development

```bash
composer install
```

## License

MIT
