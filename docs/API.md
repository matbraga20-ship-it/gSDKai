# REST API Reference

The OpenAI Content Toolkit provides a RESTful JSON API for integrating content generation into your applications.

## Base URL

```
http://localhost:8000/api
```

In production, replace with your actual domain.

## Authentication

The API does **not require authentication** for basic health and generation endpoints. Rate limiting is applied per IP address (30 requests/minute).

For admin features, use the admin panel login.

## Response Format

All API responses follow a standardized JSON format:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response-specific data
  },
  "error": null,
  "meta": {
    // Optional metadata
  }
}
```

### Error Response
```json
{
  "success": false,
  "data": null,
  "error": {
    "message": "Human-readable error message",
    "code": "ERROR_CODE"
  },
  "meta": {}
}
```

## Common HTTP Status Codes

- **200 OK** - Request successful
- **400 Bad Request** - Invalid input or validation error
- **404 Not Found** - Endpoint not found
- **429 Too Many Requests** - Rate limit exceeded
- **500 Internal Server Error** - Server error
- **503 Service Unavailable** - OpenAI API error

## Rate Limiting Headers

All responses include rate limit information:

```
X-RateLimit-Limit: 30
X-RateLimit-Remaining: 28
X-RateLimit-Reset: 1673491234
```

## Endpoints

### Health Check

**GET** `/api/health`

Check API health and configuration status.

**Parameters:** None

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "timestamp": "2026-01-13 10:30:45",
    "php_version": "8.2.1",
    "config_valid": true,
    "storage_writable": true,
    "api_key_configured": true
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl http://localhost:8000/api/health
```

---

### Generate Title

**POST** `/api/generate/title`

Generate an SEO-optimized title from content.

**Request:**
```json
{
  "content": "Your content text here. Can be up to 5000 characters."
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "result": "Amazing Guide to Content Creation",
    "type": "title",
    "model": "gpt-4o-mini",
    "usage": 25
  },
  "error": null,
  "meta": {}
}
```

**Validation:**
- `content`: Required, 10-5000 characters

**Example:**
```bash
curl -X POST http://localhost:8000/api/generate/title \
  -H "Content-Type: application/json" \
  -d '{
    "content": "A comprehensive guide about AI, machine learning, and neural networks for beginners"
  }'
```

---

### Generate Description

**POST** `/api/generate/description`

Generate a meta description (150-160 characters) optimized for search engines.

**Request:**
```json
{
  "content": "Your content or keywords here"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "result": "Learn AI basics: explore machine learning and neural networks. Perfect guide for beginners.",
    "type": "description",
    "model": "gpt-4o-mini",
    "usage": 28
  },
  "error": null,
  "meta": {}
}
```

**Validation:**
- `content`: Required, 10-5000 characters

**Example:**
```bash
curl -X POST http://localhost:8000/api/generate/description \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Machine learning article"
  }'
```

---

### Generate Tags

**POST** `/api/generate/tags`

Generate relevant tags for content classification.

**Request:**
```json
{
  "content": "Your content text"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "result": "ai, machine-learning, neural-networks, deep-learning, nlp, computer-vision, data-science, automation",
    "type": "tags",
    "model": "gpt-4o-mini",
    "usage": 22
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/generate/tags \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Article about artificial intelligence"
  }'
```

---

### Generate Timestamps/Chapters

**POST** `/api/generate/timestamps`

Generate chapter breakdowns and timestamps from a video transcript.

**Request:**
```json
{
  "transcript": "Full video transcript text here..."
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "chapters": [
      {
        "timestamp": "00:00:00",
        "title": "Introduction"
      },
      {
        "timestamp": "00:01:30",
        "title": "What is Machine Learning"
      },
      {
        "timestamp": "00:05:45",
        "title": "Types of Algorithms"
      }
    ],
    "model": "gpt-4o-mini",
    "usage": 156
  },
  "error": null,
  "meta": {}
}
```

**Validation:**
- `transcript`: Required, 50-10000 characters

**Example:**
```bash
curl -X POST http://localhost:8000/api/generate/timestamps \
  -H "Content-Type: application/json" \
  -d '{
    "transcript": "[Full video transcript here with dialogue and content descriptions]"
  }'
```

---

### Generate Shorts Ideas

**POST** `/api/generate/shorts-ideas`

Generate platform-specific short-form video ideas.

**Request:**
```json
{
  "content": "Your content or topic",
  "platform": "tiktok"
}
```

**Parameters:**
- `content`: Required, 20-5000 characters
- `platform`: Required, one of: `tiktok`, `reels`, `shorts`

**Response:**
```json
{
  "success": true,
  "data": {
    "ideas": [
      "Create a trending sound mashup showing AI predictions vs reality - 30 seconds",
      "Quick tips format: 5 AI facts you didn't know - rapid cuts with text overlays",
      "Before/after transformation using AI tools - side-by-side comparison",
      "Reaction video to AI-generated content - funny or surprising results"
    ],
    "platform": "tiktok",
    "model": "gpt-4o-mini",
    "usage": 134
  },
  "error": null,
  "meta": {}
}
```

**Validation:**
- `content`: Required, 20-5000 characters
- `platform`: Must be one of: `tiktok`, `reels`, `shorts`

**Example:**
```bash
# TikTok ideas
curl -X POST http://localhost:8000/api/generate/shorts-ideas \
  -H "Content-Type: application/json" \
  -d '{
    "content": "How to use ChatGPT for content creation",
    "platform": "tiktok"
  }'

# Instagram Reels
curl -X POST http://localhost:8000/api/generate/shorts-ideas \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Productivity tips for remote workers",
    "platform": "reels"
  }'

# YouTube Shorts
curl -X POST http://localhost:8000/api/generate/shorts-ideas \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Web development tutorials for beginners",
    "platform": "shorts"
  }'
```

---

## Embeddings

### Create Embeddings

**POST** `/api/embeddings`

Create vector embeddings for text.

**Request:**
```json
{
  "input": "Text to embed"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "embeddings": {
      "object": "list",
      "data": [
        {
          "object": "embedding",
          "embedding": [0.123, 0.456],
          "index": 0
        }
      ]
    }
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/embeddings \
  -H "Content-Type: application/json" \
  -d '{
    "input": "Semantic search example"
  }'
```

---

## Images

### Generate Images

**POST** `/api/images/generate`

Generate images from a text prompt.

**Request:**
```json
{
  "prompt": "A futuristic city at sunset",
  "options": {
    "size": "1024x1024",
    "n": 1
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "images": {
      "data": [
        {
          "b64_json": "base64string..."
        }
      ]
    }
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/images/generate \
  -H "Content-Type: application/json" \
  -d '{
    "prompt": "Minimalist logo in black and gold",
    "options": { "size": "512x512", "n": 1 }
  }'
```

---

## Audio

### Transcribe Audio

**POST** `/api/audio/transcribe`

Transcribe an audio file. This endpoint expects **multipart/form-data**.

**Request (form-data):**
- `file` (required): audio file
- `language` (optional): language code (e.g., `en`)

**Response:**
```json
{
  "success": true,
  "data": {
    "transcription": {
      "text": "Transcribed text here."
    }
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/audio/transcribe \
  -F "file=@/path/to/audio.mp3" \
  -F "language=en"
```

---

## Moderation

### Moderate Content

**POST** `/api/moderation`

Check content against OpenAI moderation policies.

**Request:**
```json
{
  "input": "Text to moderate"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "mod_123",
    "results": [
      {
        "flagged": false
      }
    ]
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/moderation \
  -H "Content-Type: application/json" \
  -d '{
    "input": "Check this text for safety."
  }'
```

---

## Models

### List Models

**GET** `/api/models`

List available OpenAI models.

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      { "id": "gpt-4o-mini", "object": "model" }
    ]
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl http://localhost:8000/api/models
```

---

## Files

### List Files

**GET** `/api/files`

List files associated with the OpenAI account.

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      { "id": "file_123", "filename": "data.jsonl" }
    ]
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl http://localhost:8000/api/files
```

### Upload File

**POST** `/api/files/upload`

Upload a file using **multipart/form-data**.

**Request (form-data):**
- `file` (required): file to upload
- `purpose` (optional): e.g., `fine-tune`, `assistants`, `vision`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "file_123",
    "filename": "data.jsonl"
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/files/upload \
  -F "file=@/path/to/data.jsonl" \
  -F "purpose=fine-tune"
```

---

## OpenAI API Explorer

### Proxy Any OpenAI Endpoint

**POST** `/api/openai/request`

Send a raw request to any OpenAI endpoint. Useful for Assistants, Threads, Runs, Vector Stores, Batches, Fine-tuning, Realtime sessions, or any future endpoints.

**Request:**
```json
{
  "method": "POST",
  "endpoint": "/assistants",
  "payload": {
    "name": "Content Helper",
    "model": "gpt-4o-mini",
    "instructions": "You are a helpful assistant."
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "asst_123",
    "object": "assistant"
  },
  "error": null,
  "meta": {}
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/openai/request \
  -H "Content-Type: application/json" \
  -d '{
    "method": "POST",
    "endpoint": "/vector_stores",
    "payload": { "name": "Docs Index" }
  }'
```

---

## Error Codes

| Code | Status | Description |
|------|--------|-------------|
| `VALIDATION_ERROR` | 400 | Input validation failed |
| `INVALID_JSON` | 400 | Invalid JSON in request body |
| `NOT_FOUND` | 404 | Endpoint not found |
| `METHOD_NOT_ALLOWED` | 405 | HTTP method not supported |
| `RATE_LIMIT_EXCEEDED` | 429 | Rate limit exceeded (30/min per IP) |
| `OPENAI_ERROR` | 503 | OpenAI API error |
| `SERVER_ERROR` | 500 | Internal server error |

## Validation Error Response

When validation fails, the response includes detailed error information:

```json
{
  "success": false,
  "data": null,
  "error": {
    "message": "Validation failed: content is required",
    "code": "VALIDATION_ERROR"
  },
  "meta": {
    "errors": {
      "content": "Content field is required"
    }
  }
}
```

## Rate Limiting

Each IP address is limited to **30 requests per minute** for all `/api/*` endpoints except `/api/health`.

When rate limit is exceeded:

```json
{
  "success": false,
  "data": null,
  "error": {
    "message": "Rate limit exceeded: 30 requests per minute",
    "code": "RATE_LIMIT_EXCEEDED"
  },
  "meta": {}
}
```

HTTP Status: **429 Too Many Requests**

**Recovery:** Wait 60 seconds before making new requests.

## Integration Examples

### JavaScript/Fetch

```javascript
async function generateTitle(content) {
  const response = await fetch('/api/generate/title', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ content: content })
  });

  const result = await response.json();

  if (result.success) {
    console.log('Generated title:', result.data.result);
  } else {
    console.error('Error:', result.error.message);
  }
}

generateTitle('My article about AI');
```

### PHP

```php
<?php
$client = new \GuzzleHttp\Client();

$response = $client->post('http://localhost:8000/api/generate/title', [
    'json' => [
        'content' => 'My article content'
    ]
]);

$data = json_decode($response->getBody(), true);

if ($data['success']) {
    echo $data['data']['result'];
}
?>
```

### Python

```python
import requests
import json

response = requests.post('http://localhost:8000/api/generate/title', 
    json={'content': 'My article content'},
    headers={'Content-Type': 'application/json'}
)

data = response.json()

if data['success']:
    print(data['data']['result'])
else:
    print(f"Error: {data['error']['message']}")
```

### cURL

```bash
curl -X POST http://localhost:8000/api/generate/title \
  -H "Content-Type: application/json" \
  -d '{"content":"Article about AI"}' | jq
```

## API Limits

- **Request timeout**: 30 seconds (configurable)
- **Max content length**: 5,000 characters (for text endpoints)
- **Max transcript length**: 10,000 characters
- **Max output tokens**: 800 (configurable, 1-4000)
- **Rate limit**: 30 requests/minute per IP
- **Concurrent requests**: No limit (but rate limiting applies)

## WebSocket Support

WebSockets are not currently supported. Use polling or webhooks for real-time updates.

## Changelog

### Version 1.0.0 (2026-01-13)
- Initial release
- All 5 content generation endpoints
- Rate limiting and logging
- Health check endpoint

---

For more information, see [README.md](README.md) and [SDK.md](SDK.md)
