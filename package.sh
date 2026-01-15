#!/usr/bin/env bash
set -e

OUT=release-$(date +%Y%m%d%H%M%S).zip
EXCLUDES=("vendor/*" "*.log" "*.sql" "composer.phar" ".phpunit.result.cache" "storage/logs/*" "storage/cache/*")

# Build exclude args for zip
EXARGS=()
for e in "${EXCLUDES[@]}"; do
  EXARGS+=( -x "$e" )
done

# Create zip (requires zip)
zip -r "$OUT" . ${EXARGS[@]}

echo "Created $OUT"
