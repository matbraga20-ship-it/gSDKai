# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-13

### Added
- Initial release of OpenAI Content Toolkit SDK
- OpenAI Responses API integration with retry logic and exponential backoff
- Five content generation endpoints:
  - Title generation (SEO-optimized)
  - Meta description generation
  - Tag generation
  - Transcript to chapters/timestamps conversion
  - Platform-specific shorts ideas (TikTok, Instagram Reels, YouTube Shorts)
- REST API with standardized JSON response format
- Rate limiting (30 requests/minute per IP) with file-based tracking
- Comprehensive logging with Monolog
- Admin panel with authentication and CSRF protection
- Interactive playground for testing all features
- Configuration management system (storage/app/config.json)
- Secure session handling with HttpOnly cookies
- Input validation and error handling
- CSRF token protection on all forms
- API key testing tool
- System health dashboard
- Full documentation (README, INSTALL, API, SDK)
- Docker and docker-compose configuration
- PHP 8.2+ with PSR-4 autoloading
- Composer package management

### Features
- **Security**: API keys stored outside webroot, CSRF protection, secure sessions
- **Reliability**: Exponential backoff retries, comprehensive error handling
- **Performance**: Configurable timeouts, rate limiting, token usage tracking
- **Developer Experience**: Well-documented, easy to integrate, clean architecture
- **UI/UX**: Responsive Tailwind CSS interface, intuitive controls

## [Unreleased]

### Planned for future releases
- Support for additional OpenAI models as they become available
- Webhook support for async processing
- Advanced analytics and usage tracking
- Batch processing API
- WebSocket support for real-time streaming
- Multi-user admin panel
- Password-protected API keys
- Custom prompt templates
- Result caching system
- API usage billing integration
- Translation support
- Mobile app companion

---

For installation and usage, see [INSTALL.md](docs/INSTALL.md) and [README.md](docs/README.md).
