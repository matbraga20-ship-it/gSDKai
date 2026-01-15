<?php
// Attempt to locate the WAMP PHP installation and update php.ini to point to a CA bundle.

$wampPhpRoot = 'C:\\wamp64\\bin\\php';
$projectRoot = dirname(__DIR__);
$bundledCert = $projectRoot . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR . 'cacert.pem';

$phpIni = null;

// Look for the most recent PHP folder in WAMP
if (is_dir($wampPhpRoot)) {
    $dirs = array_filter(glob($wampPhpRoot . '\\*'), 'is_dir');
    // Sort descending to pick latest version-like folder
    usort($dirs, function($a, $b) { return strcmp($b, $a); });
    foreach ($dirs as $d) {
        $candidate = $d . DIRECTORY_SEPARATOR . 'php.ini';
        if (is_file($candidate)) { $phpIni = $candidate; break; }
    }
}

// If not found via WAMP layout, try to use the loaded php.ini when running under PHP
if (!$phpIni && function_exists('php_ini_loaded_file')) {
    $loaded = php_ini_loaded_file();
    if ($loaded && is_file($loaded)) { $phpIni = $loaded; }
}

if (!$phpIni) {
    echo "Could not locate php.ini. Please run this script with the WAMP PHP binary or update php.ini manually.\n";
    exit(1);
}

if (!file_exists($bundledCert)) {
    echo "No bundled cacert.pem found at: $bundledCert\n";
    echo "Please download a certificate bundle (https://curl.se/ca/cacert.pem) and place it at that path, or update php.ini manually.\n";
    exit(1);
}

$lines = file($phpIni, FILE_IGNORE_NEW_LINES);
$foundCurl = false;
$foundOpen = false;
foreach ($lines as $i => $line) {
    if (preg_match('/^\s*;?\s*curl.cainfo\s*=\s*/i', $line)) {
        $lines[$i] = "curl.cainfo = \"$bundledCert\"";
        $foundCurl = true;
    }
    if (preg_match('/^\s*;?\s*openssl.cafile\s*=\s*/i', $line)) {
        $lines[$i] = "openssl.cafile = \"$bundledCert\"";
        $foundOpen = true;
    }
}
if (!$foundCurl) { $lines[] = ""; $lines[] = "curl.cainfo = \"$bundledCert\""; }
if (!$foundOpen) { $lines[] = "openssl.cafile = \"$bundledCert\""; }

file_put_contents($phpIni, implode(PHP_EOL, $lines) . PHP_EOL);
echo "Updated php.ini ($phpIni) with CA paths pointing to $bundledCert\n";
