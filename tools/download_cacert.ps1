$outDir = Join-Path (Get-Location) 'certs'
if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Path $outDir | Out-Null }
$outFile = Join-Path $outDir 'cacert.pem'
$url = 'https://curl.se/ca/cacert.pem'
Write-Host "Downloading CA bundle from $url to $outFile"
try {
    Invoke-WebRequest -Uri $url -OutFile $outFile -UseBasicParsing -ErrorAction Stop
    Write-Host "Downloaded cacert.pem to $outFile"
} catch {
    Write-Host "Failed to download cacert.pem: $_" -ForegroundColor Red
}
