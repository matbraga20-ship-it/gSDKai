# OpenAI Content Toolkit SDK

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/php-%3E%3D%208.2-blue)
![License](https://img.shields.io/badge/license-MIT-green)

A professional, CodeCanyon-ready PHP SDK for the OpenAI API with REST API endpoints, admin panel, and interactive playground. Perfect for building content generation applications.

## ✨ Features

### SDK Features
- **OpenAI Responses API Integration**: Direct integration with OpenAI's latest Responses API endpoint
- **Multiple Content Generation Services**:
  - Title Generator (SEO-optimized)
  - Meta Description Generator
  - Tag Generator
  - Transcript Chapters/Timestamps
  - Short-form Video Ideas (TikTok, Reels, Shorts)
- **Robust Error Handling**: Custom exceptions with detailed error context
- **Retry Logic**: Exponential backoff for transient failures (1s → 2s → 4s)
- **Rate Limiting**: Built-in per-IP rate limiting (30 requests/minute)
- **Comprehensive Logging**: File-based logging with Monolog

### REST API
- RESTful JSON endpoints for all content generation features
- Standardized response format
- Rate limit headers
- Comprehensive error responses
- Health check endpoint

### Admin Panel
- 🔐 Secure authentication system
- ⚙️ Settings management (API key, model, temperature, timeouts)
- 📊 Dashboard with system health checks
- 🧪 API key testing tool
- CSRF protection on all forms
- Secure session handling

### Interactive Playground
- 📝 Title Generator with real-time generation
- 📄 SEO Description Generator
- ⏱️ Transcript-to-Chapters converter
- 🎬 Shorts Ideas generator (platform-specific)
- Copy-to-clipboard functionality
- Loading states and error handling

## 🚀 Quick Start

### Requirements
- PHP 8.2 or higher
- Composer
- OpenAI API key (get one at [platform.openai.com](https://platform.openai.com))

### Installation

```bash
# Clone or download the repository
cd openai-content-toolkit

# Install dependencies
composer install

# Start the built-in PHP server
php -S localhost:8000 -t public

# Open your browser to http://localhost:8000
```

### Configuration

1. **Login** to the admin panel (default credentials: `admin` / `admin`)
2. **Go to Settings** and enter your OpenAI API key
3. **Optionally adjust**:
   - Model (gpt-4o-mini recommended)
   - Temperature (0-2, default 0.7)
   - Max output tokens (1-4000, default 800)
   - API timeout (5-120s, default 30s)
4. **Test the connection** using the "Test API Key" button
5. **Use the Playground** to generate content

## 📚 Documentation

- [INSTALL.md](docs/INSTALL.md) - Detailed installation & setup guide
- [API.md](docs/API.md) - REST API reference with examples
- [SDK.md](docs/SDK.md) - PHP SDK usage guide
- [ROADMAP.md](ROADMAP.md) - Implementation checklist

## 🛠️ Project Structure

```
/
├── bootstrap.php                # Application initialization
├── composer.json                # PHP dependencies
├── ROADMAP.md                   # Implementation checklist
├── public/
│   ├── index.php               # Main UI router
│   ├── api/
│   │   └── index.php           # REST API endpoints
│   └── assets/                 # CSS, JS, images
├── src/
│   ├── Client/
│   │   └── OpenAIClient.php    # OpenAI API wrapper
│   ├── Services/
│   │   ├── TextService.php     # Title, description, tags
│   │   ├── ChaptersService.php # Transcripts to chapters
│   │   ├── ShortsIdeasService.php # Video ideas
│   │   └── PromptBuilder.php   # Safe prompt templates
│   ├── DTO/                    # Data transfer objects
│   ├── Exceptions/             # Custom exceptions
│   └── Support/
│       ├── Config.php          # Configuration manager
│       ├── Logger.php          # Application logging
│       ├── Csrf.php            # CSRF protection
│       ├── RateLimiter.php     # Request rate limiting
│       ├── Validator.php       # Input validation
│       └── ResponseJson.php    # API responses
├── config/
│   └── config.php              # Default configuration
├── storage/
│   ├── app/
│   │   └── config.json         # User configuration (created automatically)
│   ├── logs/
│   │   └── app.log             # Application logs
│   └── cache/                  # Rate limit counters
├── views/
│   ├── layout.php              # Master layout
│   ├── login.php               # Login page
│   ├── dashboard.php           # Dashboard
│   ├── settings.php            # Settings panel
│   └── playground.php          # Interactive playground
└── docs/
    ├── README.md
    ├── INSTALL.md
    ├── API.md
    ├── SDK.md
    └── img/                    # Screenshots
```

## 🔐 Security Features

- **Secure Configuration**: API keys stored outside webroot in `storage/app/config.json`
- **CSRF Protection**: Token generation and validation on all forms
- **Session Security**: HttpOnly, SameSite cookie settings
- **Rate Limiting**: Per-IP request limiting (30/min)
- **Input Validation**: Comprehensive validation of all inputs
- **Error Handling**: Never exposes sensitive information in error messages
- **Logging**: All API calls and errors are logged

## 📊 REST API Endpoints

### Health Check
```bash
GET /api/health
```

### Generate Title
```bash
POST /api/generate/title
Content-Type: application/json

{
  "content": "Your content text here..."
}
```

### Generate Description
```bash
POST /api/generate/description
Content-Type: application/json

{
  "content": "Your content or keywords here..."
}
```

### Generate Tags
```bash
POST /api/generate/tags
Content-Type: application/json

{
  "content": "Your content text here..."
}
```

### Generate Timestamps/Chapters
```bash
POST /api/generate/timestamps
Content-Type: application/json

{
  "transcript": "Your video transcript here..."
}
```

### Generate Shorts Ideas
```bash
POST /api/generate/shorts-ideas
Content-Type: application/json

{
  "content": "Your content here...",
  "platform": "tiktok"  // or "reels", "shorts"
}
```

## 🐳 Docker (Optional)

A Dockerfile is included for containerized deployment:

```bash
docker build -t openai-toolkit .
docker run -p 8000:8000 openai-toolkit
```

## 💻 Example Usage (PHP SDK)

```php
<?php
require 'vendor/autoload.php';

use OpenAI\Support\Config;
use OpenAI\Services\TextService;

// Initialize config
Config::load();

// Generate a title
$service = new TextService();
$title = $service->generateTitle('This is my content...');

echo $title->result;
```

## 🔄 Models Supported

The toolkit supports all OpenAI models. Recommended:
- **gpt-4o-mini** (Fast, affordable, recommended)
- **gpt-4o** (More powerful)
- **gpt-4-turbo** (Advanced reasoning)

## 📝 License

MIT License - see LICENSE file for details

## 🤝 Support

For issues, feature requests, or documentation improvements, please refer to the official OpenAI API documentation at [platform.openai.com/docs](https://platform.openai.com/docs).

## 🎯 Typical Use Cases

- **Content Creation Platforms**: Auto-generate titles, descriptions, and SEO metadata
- **Video Production**: Generate chapters and timestamps from transcripts
- **Social Media**: Create platform-specific short-form video ideas
- **E-commerce**: Generate product titles and descriptions
- **Marketing Agencies**: Bulk content generation and optimization

---

**Built with ❤️ for content creators and developers**
