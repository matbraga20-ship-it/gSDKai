# Installation & Setup Guide

## System Requirements

- **PHP 8.2 or higher**
- **Composer** (for dependency management)
- **OpenAI API Key** (available at https://platform.openai.com/api-keys)
- **Writable storage directory** for configuration and logs

## Step-by-Step Installation

### 1. Download & Extract

```bash
# Clone from repository or extract ZIP file
cd /path/to/openai-content-toolkit
```

### 2. Install Dependencies

```bash
composer install
```

This will install all required packages:
- `guzzlehttp/guzzle` - HTTP client for OpenAI API
- `monolog/monolog` - Logging library
- `vlucas/phpdotenv` - Environment variable loading

### 3. Verify Permissions

Ensure the `storage` directory is writable:

```bash
# On Linux/Mac
chmod -R 755 storage

# The web server user should be able to write to storage
# For LAMP stack: chown -R www-data:www-data storage
```

### 4. Start the Development Server

```bash
php -S localhost:8000 -t public
```

Then open your browser to: **http://localhost:8000**

### 5. Initial Login

1. **Login with default credentials:**
   - Username: `admin`
   - Password: `admin`

2. **⚠️ Important**: Change these credentials immediately after login (see Settings > Password)

### 6. Configure OpenAI API Key

1. **Get your API key:**
   - Visit https://platform.openai.com/api-keys
   - Click "Create new secret key"
   - Copy the key (you can only view it once!)

2. **Enter in Settings:**
   - Navigate to **Settings** in the admin panel
   - Paste your API key in the "OpenAI API Key" field
   - Select your preferred model (gpt-4o-mini recommended)
   - Click **"Test API Key"** to verify

3. **Save Configuration:**
   - Click **"Save Settings"**
   - You should see a success message

### 7. Test the System

1. Go to **Playground**
2. Try each tab:
   - **Title Generator**: Paste some text and click "Generate Title"
   - **Description Generator**: Paste content and generate
   - **Timestamps**: Paste a video transcript
   - **Shorts Ideas**: Select a platform and generate ideas

## Configuration File

After first login, a config file is created at:
```
storage/app/config.json
```

**Never commit this file to version control** - it contains your API key!

Example content:
```json
{
  "openai_api_key": "sk-...",
  "openai_model": "gpt-4o-mini",
  "openai_temperature": 0.7,
  "openai_max_output_tokens": 800,
  "openai_timeout": 30,
  "last_error": null,
  "created_at": "2026-01-13 10:00:00",
  "updated_at": "2026-01-13 10:00:00"
}
```

## Environment Setup (Optional)

You can optionally create a `.env` file in the project root:

```bash
cp .env.example .env
```

Edit `.env` with your settings:
```
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-4o-mini
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_OUTPUT_TOKENS=800
OPENAI_TIMEOUT=30
```

## Production Deployment

### Using a Web Server (Apache/Nginx)

**Apache (with mod_rewrite):**
```
<Directory /path/to/openai-toolkit/public>
    Options -MultiViews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</Directory>
```

**Nginx:**
```
location ~ /api/ {
    rewrite ^/api/(.*)$ /api/index.php last;
}

location / {
    rewrite ^(.*)$ /index.php last;
}
```

### Docker Deployment

Build and run with Docker:

```bash
# Build the image
docker build -t openai-toolkit .

# Run the container
docker run -d \
  -p 8000:8000 \
  -v $(pwd)/storage:/app/storage \
  --name openai-toolkit \
  openai-toolkit

# View logs
docker logs openai-toolkit

# Stop the container
docker stop openai-toolkit
```

The `storage` volume ensures configuration persists.

### HTTPS (Required for Production)

For production, always use HTTPS:

1. **Get SSL certificate** (LetsEncrypt recommended)
2. **Configure web server** to use HTTPS
3. **Set environment** in `bootstrap.php`:
   ```php
   ini_set('session.cookie_secure', 1);  // HTTPS only
   ```

## Troubleshooting

### "API key is not configured"
- Go to Settings
- Paste your OpenAI API key
- Click "Test API Key"
- Click "Save Settings"

### "Storage directory not writable"
```bash
# Fix permissions
chmod -R 777 storage
chown -R www-data:www-data storage  # if using Apache
```

### "Composer not found"
- Install Composer: https://getcomposer.org/download
- Or use: `php composer.phar install`

### "Rate limit exceeded"
- The app limits to 30 requests/minute per IP
- Wait 60 seconds or restart the PHP server
- Admin can reset limits in Settings

### "Invalid API key" error
- Verify the key starts with `sk-`
- Check if key has expired
- Generate a new key from https://platform.openai.com/api-keys

### "Service temporarily unavailable"
- OpenAI API may be down
- Check status: https://status.openai.com
- Try again in a few minutes

## File Structure Created on Installation

```
openai-toolkit/
├── storage/                          # Created on first run
│   ├── app/
│   │   └── config.json              # Your configuration (NEVER commit!)
│   ├── logs/
│   │   └── app.log                  # Application logs
│   └── cache/
│       └── rate_limit_*.json        # Rate limit counters (auto-cleanup)
```

## Security Checklist

- [ ] Changed default admin password
- [ ] Stored API key securely in `storage/app/config.json`
- [ ] Set `storage/` to not be publicly accessible
- [ ] Using HTTPS in production
- [ ] Backing up `storage/app/config.json` separately from version control
- [ ] Disabled directory listing in web server
- [ ] Set appropriate file permissions (755 for directories, 644 for files)

## Next Steps

1. **Read the API documentation**: [API.md](API.md)
2. **Learn the SDK**: [SDK.md](SDK.md)
3. **Explore the playground** for examples
4. **Integrate with your application** using the REST API

## Support

For issues with:
- **OpenAI API**: Check https://platform.openai.com/docs
- **PHP Code**: Review the code and error logs in `storage/logs/app.log`
- **Installation**: Ensure all requirements are met above

---

**Tip**: Always check the Dashboard for system health status and recent errors!
