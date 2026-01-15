$timestamp = Get-Date -Format yyyyMMddHHmmss
$out = "release-$timestamp.zip"
$exclude = @('vendor','*.log','*.sql','composer.phar','.phpunit.result.cache','storage\logs\*','storage\cache\*')

# Gather files to include
$files = Get-ChildItem -Recurse -File | Where-Object {
    $p = $_.FullName.Substring((Get-Location).Path.Length+1)
    -not ($exclude | ForEach-Object { $p -like "$_" })
}

if (Test-Path $out) { Remove-Item $out }

Compress-Archive -LiteralPath ($files | ForEach-Object { $_.FullName }) -DestinationPath $out
Write-Host "Created $out"
