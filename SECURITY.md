# Security Policy

This project follows basic security practices. Buyers should follow these instructions for production deployments.

- Do not commit `storage/app/config.json` or any credentials to version control.
- Move `storage/` directory outside the web root in production or block access via webserver rules.
- Use HTTPS in production and set `session.cookie_secure` in `bootstrap.php`.
- Rotate OpenAI API keys periodically and do not share keys in public repositories.
- Sanitize and validate user input server-side. The SDK includes `OpenAI\Support\Validator` for common checks.

If you discover a security vulnerability, report it following the `SUPPORT.md` guide.
