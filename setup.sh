#!/usr/bin/env bash
set -e

echo "Running setup for OpenAI Content Toolkit SDK..."

# Install composer deps if not present
if [ ! -f vendor/autoload.php ]; then
  if command -v composer >/dev/null 2>&1; then
    composer install
  else
    echo "Composer not found. Please install Composer and run 'composer install'"
  fi
fi

# Create storage dirs
mkdir -p storage/logs storage/cache storage/app

# Ensure .gitkeep in logs/cache
touch storage/logs/.gitkeep
touch storage/cache/.gitkeep

# Copy example config if not exists
if [ ! -f storage/app/config.json ]; then
  if [ -f storage/app/config.example.json ]; then
    cp storage/app/config.example.json storage/app/config.json
    echo "Copied example config to storage/app/config.json"
  else
    echo "{\"openai_api_key\": \"\", \"openai_model\": \"gpt-4o-mini\"}" > storage/app/config.json
    echo "Created default storage/app/config.json"
  fi
fi

chmod -R 775 storage || true

echo "Setup completed. Run: php -S localhost:8000 -t public" 
