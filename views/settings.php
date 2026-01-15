<?php
/**
 * Settings page view
 */

use OpenAI\Support\Config;
use OpenAI\Support\Csrf;

Config::load();
$config = Config::all();
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Settings</h1>
        <p class="text-gray-600">Configure OpenAI API and application defaults</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="?page=settings" class="bg-white rounded-lg shadow-md p-6 space-y-6">
                <?php echo Csrf::field(); ?>

                <!-- API Key -->
                <div>
                    <label for="openai_api_key" class="block text-sm font-medium text-gray-900 mb-2">
                        OpenAI API Key <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="openai_api_key"
                        name="openai_api_key"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        value="<?php echo htmlspecialchars($config['openai_api_key']); ?>"
                        placeholder="sk-..."
                    >
                    <p class="text-xs text-gray-600 mt-1">Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">platform.openai.com/api-keys</a></p>
                </div>

                <!-- Model Selection -->
                <div>
                    <label for="openai_model" class="block text-sm font-medium text-gray-900 mb-2">
                        Model <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="openai_model"
                        name="openai_model"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="gpt-4o-mini" <?php echo $config['openai_model'] === 'gpt-4o-mini' ? 'selected' : ''; ?>>gpt-4o-mini (Fast, affordable)</option>
                        <option value="gpt-4o" <?php echo $config['openai_model'] === 'gpt-4o' ? 'selected' : ''; ?>>gpt-4o (More powerful)</option>
                        <option value="gpt-4-turbo" <?php echo $config['openai_model'] === 'gpt-4-turbo' ? 'selected' : ''; ?>>gpt-4-turbo (Advanced)</option>
                    </select>
                    <p class="text-xs text-gray-600 mt-1">gpt-4o-mini is recommended for most use cases</p>
                </div>

                <!-- Temperature -->
                <div>
                    <label for="openai_temperature" class="block text-sm font-medium text-gray-900 mb-2">
                        Temperature: <span class="text-blue-600 font-semibold"><?php echo number_format((float)$config['openai_temperature'], 1); ?></span>
                    </label>
                    <input
                        type="range"
                        id="openai_temperature"
                        name="openai_temperature"
                        min="0"
                        max="2"
                        step="0.1"
                        value="<?php echo htmlspecialchars($config['openai_temperature']); ?>"
                        class="w-full"
                    >
                    <p class="text-xs text-gray-600 mt-1">0 = Deterministic (consistent), 2 = Creative (unpredictable)</p>
                </div>

                <!-- Max Output Tokens -->
                <div>
                    <label for="openai_max_output_tokens" class="block text-sm font-medium text-gray-900 mb-2">
                        Max Output Tokens <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="number"
                        id="openai_max_output_tokens"
                        name="openai_max_output_tokens"
                        min="1"
                        max="4000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        value="<?php echo htmlspecialchars($config['openai_max_output_tokens']); ?>"
                    >
                    <p class="text-xs text-gray-600 mt-1">Maximum tokens in API responses (1-4000)</p>
                </div>

                <!-- Timeout -->
                <div>
                    <label for="openai_timeout" class="block text-sm font-medium text-gray-900 mb-2">
                        API Request Timeout (seconds) <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="number"
                        id="openai_timeout"
                        name="openai_timeout"
                        min="5"
                        max="120"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        value="<?php echo htmlspecialchars($config['openai_timeout']); ?>"
                    >
                    <p class="text-xs text-gray-600 mt-1">How long to wait for API responses (5-120 seconds)</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4 border-t border-gray-200">
                    <button
                        type="submit"
                        name="action"
                        value="save"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium"
                    >
                        💾 Save Settings
                    </button>
                    <button
                        type="submit"
                        name="action"
                        value="test"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium"
                    >
                        🧪 Test API Key
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Panel -->
        <div class="lg:col-span-1">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 sticky top-4">
                <h3 class="font-semibold text-blue-900 mb-4">❓ Help</h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">API Key</h4>
                        <p class="text-blue-800">Get your free API key from OpenAI. New users get $5 in free credits.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Models</h4>
                        <p class="text-blue-800">gpt-4o-mini is fastest and cheapest. Use gpt-4o for better quality.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Temperature</h4>
                        <p class="text-blue-800">Lower = more consistent. Higher = more creative. Try 0.7 for balanced results.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Rate Limiting</h4>
                        <p class="text-blue-800">This app limits requests to 30 per minute per IP address.</p>
                    </div>
                </div>

                <hr class="my-4 border-blue-200">

                <a href="https://platform.openai.com/api-keys" target="_blank" class="block px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700 transition font-medium">
                    Get API Key →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Update temperature display in real-time
document.getElementById('openai_temperature').addEventListener('input', function(e) {
    const value = (Math.round(e.target.value * 10) / 10).toFixed(1);
    document.querySelector('[class*="text-blue-600"]').textContent = value;
});
</script>
