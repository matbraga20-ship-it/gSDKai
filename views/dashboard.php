<?php
/**
 * Dashboard view
 */

use OpenAI\Support\Config;

Config::load();

$configErrors = Config::validate();
$hasApiKey = Config::hasApiKey();
$phpVersion = PHP_VERSION;
$storageLogsWritable = is_writable(STORAGE_LOGS_PATH);
$storageCacheWritable = is_writable(STORAGE_CACHE_PATH);
$lastError = Config::get('last_error');
$requestCount = \OpenAI\Support\RateLimiter::getCount();
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">System status and configuration overview</p>
    </div>

    <!-- System Health -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- PHP Version -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">PHP Version</h3>
            <p class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($phpVersion); ?></p>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo (version_compare(PHP_VERSION, '8.2') >= 0) ? '✅ OK' : '⚠️ PHP 8.2+ required'; ?>
            </p>
        </div>

        <!-- API Key Status -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 <?php echo $hasApiKey ? 'border-green-500' : 'border-red-500'; ?>">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">OpenAI API Key</h3>
            <?php if ($hasApiKey): ?>
                <p class="text-lg font-semibold text-green-600">✅ Configured</p>
                <p class="text-sm text-gray-600 mt-1"><?php echo Config::getApiKeyMasked(); ?></p>
            <?php else: ?>
                <p class="text-lg font-semibold text-red-600">❌ Not Configured</p>
                <p class="text-sm text-gray-600 mt-1"><a href="?page=settings" class="text-blue-600 hover:underline">Configure in Settings →</a></p>
            <?php endif; ?>
        </div>

        <!-- Storage Health -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Storage Directories</h3>
            <div class="space-y-1 text-sm">
                <p><?php echo $storageLogsWritable ? '✅' : '❌'; ?> Logs directory (<?php echo STORAGE_LOGS_PATH; ?>)</p>
                <p><?php echo $storageCacheWritable ? '✅' : '❌'; ?> Cache directory (<?php echo STORAGE_CACHE_PATH; ?>)</p>
            </div>
            <?php if (!$storageLogsWritable || !$storageCacheWritable): ?>
                <p class="text-xs text-red-600 mt-2">⚠️ Some directories are not writable</p>
            <?php endif; ?>
        </div>

        <!-- Current Rate Limit -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Rate Limit Status</h3>
            <p class="text-2xl font-bold text-yellow-600"><?php echo $requestCount; ?>/30</p>
            <p class="text-sm text-gray-600 mt-1">Requests this minute</p>
        </div>
    </div>

    <!-- Configuration Status -->
    <?php if (!empty($configErrors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-4">⚠️ Configuration Issues</h3>
            <ul class="space-y-2">
                <?php foreach ($configErrors as $field => $error): ?>
                    <li class="text-sm text-red-700">
                        <strong><?php echo htmlspecialchars($field); ?>:</strong> <?php echo htmlspecialchars($error); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="text-sm text-red-600 mt-4">
                <a href="?page=settings" class="underline">Fix these issues in Settings →</a>
            </p>
        </div>
    <?php else: ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-900">✅ Configuration Valid</h3>
            <p class="text-sm text-green-700 mt-2">All settings are configured correctly and the system is ready to use.</p>
        </div>
    <?php endif; ?>

    <!-- Last API Error -->
    <?php if ($lastError): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">Last API Error</h3>
            <p class="text-sm text-yellow-700"><?php echo htmlspecialchars($lastError['message']); ?></p>
            <p class="text-xs text-yellow-600 mt-1"><?php echo htmlspecialchars($lastError['timestamp']); ?></p>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="?page=settings" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm font-medium">
                ⚙️ Configure Settings
            </a>
            <a href="?page=playground" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm font-medium">
                🎮 Go to Playground
            </a>
            <a href="?page=settings&test_key=1" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm font-medium" onclick="return confirm('Test API connection?')">
                🧪 Test API Key
            </a>
        </div>
    </div>
</div>
