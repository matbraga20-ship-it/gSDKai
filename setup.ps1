Write-Host "Running setup for OpenAI Content Toolkit SDK..."

# Detect PHP executable (prefer WAMP PHP if available)
$phpExe = $null
try {
    # Look for common WAMP installation path
    $wampRoot = 'C:\\wamp64\\bin\\php'
    if (Test-Path $wampRoot) {
        $dirs = Get-ChildItem -Path $wampRoot -Directory | Sort-Object Name -Descending
        if ($dirs.Count -gt 0) {
            $phpCandidate = Join-Path $dirs[0].FullName 'php.exe'
            if (Test-Path $phpCandidate) { $phpExe = $phpCandidate }
        }
    }

    # Fallback to global php on PATH
    if (-not $phpExe) {
        $globalPhp = Get-Command php -ErrorAction SilentlyContinue
        if ($globalPhp) { $phpExe = $globalPhp.Source }
    }
} catch {
    $phpExe = $null
}

if ($phpExe) { Write-Host "Using PHP executable: $phpExe" } else { Write-Host "No PHP executable found in WAMP or PATH. Ensure PHP is installed." -ForegroundColor Yellow }

# Run Composer install using the detected PHP or composer command
$composerCmd = Get-Command composer -ErrorAction SilentlyContinue
if ($composerCmd) {
    Write-Host "Running: composer install"
    & composer install
} elseif (Test-Path 'composer.phar') {
    if ($phpExe) {
        Write-Host "Running: $phpExe composer.phar install"
        & $phpExe 'composer.phar' 'install'
    } else {
        Write-Host "Found composer.phar but no PHP executable available to run it. Please install PHP or run composer manually." -ForegroundColor Yellow
    }
} else {
    Write-Host "Composer not found. Please install Composer or place composer.phar in the project root." -ForegroundColor Yellow
}

# Create storage directories
$dirs = @('storage', 'storage\\logs', 'storage\\cache', 'storage\\app')
foreach ($d in $dirs) {
    if (-not (Test-Path $d)) { New-Item -ItemType Directory -Path $d | Out-Null }
}

# Create gitkeep files
New-Item -Path storage\\logs\\.gitkeep -ItemType File -Force | Out-Null
New-Item -Path storage\\cache\\.gitkeep -ItemType File -Force | Out-Null

# Copy example config if not exists
if (-not (Test-Path 'storage\\app\\config.json')) {
    if (Test-Path 'storage\\app\\config.example.json') {
        Copy-Item 'storage\\app\\config.example.json' 'storage\\app\\config.json'
        Write-Host 'Copied example config to storage/app/config.json'
    } else {
        '{"openai_api_key": "", "openai_model": "gpt-4o-mini"}' | Out-File -FilePath 'storage\\app\\config.json' -Encoding UTF8
        Write-Host 'Created default storage/app/config.json'
    }
}

Write-Host 'Setup completed. Run: & "<path-to-wamp-php>" -S localhost:8000 -t public (or use global php)' -ForegroundColor Green
Write-Host "Running setup for OpenAI Content Toolkit SDK..."

# Ensure composer is available
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "Composer not found. Please install Composer and run 'composer install' manually." -ForegroundColor Yellow
} else {
    composer install
}

# Create storage directories
$dirs = @('storage', 'storage\logs', 'storage\cache', 'storage\app')
foreach ($d in $dirs) {
    if (-not (Test-Path $d)) { New-Item -ItemType Directory -Path $d | Out-Null }
}

# Create gitkeep files
New-Item -Path storage\logs\.gitkeep -ItemType File -Force | Out-Null
New-Item -Path storage\cache\.gitkeep -ItemType File -Force | Out-Null

# Copy example config if not exists
if (-not (Test-Path 'storage\app\config.json')) {
    if (Test-Path 'storage\app\config.example.json') {
        Copy-Item 'storage\app\config.example.json' 'storage\app\config.json'
        Write-Host 'Copied example config to storage/app/config.json'
    } else {
        '{"openai_api_key": "", "openai_model": "gpt-4o-mini"}' | Out-File -FilePath 'storage\app\config.json' -Encoding UTF8
        Write-Host 'Created default storage/app/config.json'
    }
}

Write-Host 'Setup completed. Run: php -S localhost:8000 -t public' -ForegroundColor Green
