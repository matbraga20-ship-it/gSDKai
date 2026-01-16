<?php
/**
 * Playground view
 * Interactive tabs for testing all generation features
 */

use OpenAI\Support\Config;

Config::load();
$hasApiKey = Config::hasApiKey();
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Playground</h1>
        <p class="text-gray-600">Test all content generation features in real-time</p>
    </div>

    <?php if (!$hasApiKey): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">⚠️ API Key Not Configured</h3>
            <p class="text-sm text-yellow-700">Please configure your OpenAI API key in <a href="?page=settings" class="underline">Settings</a> before using the playground.</p>
        </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-md border-b border-gray-200">
        <div class="flex flex-wrap">
            <button data-tab="title" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition bg-blue-50 text-blue-700 border-b-blue-600" onclick="switchTab('title')">
                📝 Title Generator
            </button>
            <button data-tab="description" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('description')">
                📄 Description Generator
            </button>
            <button data-tab="tags" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('tags')">
                🏷️ Tag Generator
            </button>
            <button data-tab="responses" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('responses')">
                💬 Responses
            </button>
            <button data-tab="chapters" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('chapters')">
                ⏱️ Timestamps/Chapters
            </button>
            <button data-tab="shorts" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('shorts')">
                🎬 Shorts Ideas
            </button>
            <button data-tab="embeddings" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('embeddings')">
                🔗 Embeddings
            </button>
            <button data-tab="images" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('images')">
                🖼️ Image Generation
            </button>
            <button data-tab="audio" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('audio')">
                🎙️ Audio Transcription
            </button>
            <button data-tab="moderation" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('moderation')">
                🛡️ Moderation
            </button>
            <button data-tab="models" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('models')">
                📚 Models
            </button>
            <button data-tab="files" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('files')">
                📁 Files
            </button>
            <button data-tab="explorer" class="tab-button px-4 py-3 font-medium text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition" onclick="switchTab('explorer')">
                🧭 API Explorer
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Title Generator Tab -->
        <div id="tab-title" class="tab-content active space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">📝 Title Generator</h2>
            <p class="text-gray-600">Generate compelling, SEO-friendly titles from your content</p>

            <textarea
                id="title-input"
                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Paste your content here..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button
                onclick="generateTitle()"
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ✨ Generate Title
            </button>

            <div id="title-result" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="font-semibold text-green-900 mb-2">Generated Title:</h3>
                <p id="title-output" class="text-lg text-green-800 mb-3"></p>
                <button onclick="copyToClipboard(document.getElementById('title-output').textContent)" class="text-sm text-green-600 hover:underline">
                    📋 Copy Title
                </button>
            </div>

            <div id="title-loading" class="hidden text-center">
                <p class="text-gray-600">⏳ Generating...</p>
            </div>

            <div id="title-error" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <p id="title-error-msg" class="text-red-800"></p>
            </div>
        </div>

        <!-- Description Generator Tab -->
        <div id="tab-description" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">📄 Description Generator</h2>
            <p class="text-gray-600">Generate SEO-optimized meta descriptions</p>

            <textarea
                id="description-input"
                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Paste your content or keywords here..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button
                onclick="generateDescription()"
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ✨ Generate Description
            </button>

            <div id="description-result" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="font-semibold text-green-900 mb-2">Generated Description:</h3>
                <p id="description-output" class="text-green-800 mb-2"></p>
                <p class="text-xs text-green-700 mb-3"><span id="description-length">0</span>/160 characters</p>
                <button onclick="copyToClipboard(document.getElementById('description-output').textContent)" class="text-sm text-green-600 hover:underline">
                    📋 Copy Description
                </button>
            </div>

            <div id="description-loading" class="hidden text-center">
                <p class="text-gray-600">⏳ Generating...</p>
            </div>

            <div id="description-error" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <p id="description-error-msg" class="text-red-800"></p>
            </div>
        </div>

        <!-- Tags Generator Tab -->
        <div id="tab-tags" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🏷️ Tag Generator</h2>
            <p class="text-gray-600">Generate comma-separated tags from your content</p>

            <textarea
                id="tags-input"
                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Paste your content or keywords here..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button
                onclick="generateTags()"
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ✨ Generate Tags
            </button>

            <div id="tags-result" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="font-semibold text-green-900 mb-2">Generated Tags:</h3>
                <p id="tags-output" class="text-green-800 mb-2"></p>
                <button onclick="copyToClipboard(document.getElementById('tags-output').textContent)" class="text-sm text-green-600 hover:underline">
                    📋 Copy Tags
                </button>
            </div>

            <div id="tags-loading" class="hidden text-center">
                <p class="text-gray-600">⏳ Generating...</p>
            </div>

            <div id="tags-error" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <p id="tags-error-msg" class="text-red-800"></p>
            </div>
        </div>

        <!-- Responses Tab -->
        <div id="tab-responses" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">💬 Responses API</h2>
            <p class="text-gray-600">Send a direct payload to the OpenAI Responses API</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="responses-model" class="block text-sm font-medium text-gray-900 mb-2">Model</label>
                    <input id="responses-model" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="gpt-4o-mini" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
                </div>
                <div>
                    <label for="responses-max-tokens" class="block text-sm font-medium text-gray-900 mb-2">Max Output Tokens</label>
                    <input id="responses-max-tokens" type="number" min="1" max="4000" value="120" class="w-full px-4 py-2 border border-gray-300 rounded-md" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="responses-system" class="block text-sm font-medium text-gray-900 mb-2">System Prompt</label>
                    <textarea id="responses-system" class="w-full h-24 px-4 py-3 border border-gray-300 rounded-md" placeholder="You are a helpful assistant." <?php echo !$hasApiKey ? 'disabled' : ''; ?>></textarea>
                </div>
                <div>
                    <label for="responses-user" class="block text-sm font-medium text-gray-900 mb-2">User Prompt</label>
                    <textarea id="responses-user" class="w-full h-24 px-4 py-3 border border-gray-300 rounded-md" placeholder="Write a short elevator pitch." <?php echo !$hasApiKey ? 'disabled' : ''; ?>></textarea>
                </div>
            </div>

            <button onclick="sendResponses()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">
                🚀 Send Response
            </button>

            <div id="responses-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Output Text</h3>
                <pre id="responses-output" class="text-sm text-gray-800 whitespace-pre-wrap"></pre>
            </div>
        </div>

        <!-- Timestamps/Chapters Tab -->
        <div id="tab-chapters" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">⏱️ Timestamps & Chapters</h2>
            <p class="text-gray-600">Generate chapter breakdowns from video transcripts</p>

            <textarea
                id="chapters-input"
                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Paste your video transcript here..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button
                onclick="generateChapters()"
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ✨ Generate Chapters
            </button>

            <div id="chapters-result" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="font-semibold text-green-900 mb-3">Generated Chapters:</h3>
                <div id="chapters-output" class="space-y-2 mb-3"></div>
                <button onclick="copyChaptersToClipboard()" class="text-sm text-green-600 hover:underline">
                    📋 Copy All Chapters
                </button>
            </div>

            <div id="chapters-loading" class="hidden text-center">
                <p class="text-gray-600">⏳ Generating...</p>
            </div>

            <div id="chapters-error" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <p id="chapters-error-msg" class="text-red-800"></p>
            </div>
        </div>

        <!-- Shorts Ideas Tab -->
        <div id="tab-shorts" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🎬 Shorts Ideas Generator</h2>
            <p class="text-gray-600">Generate viral short-form video ideas for TikTok, Instagram Reels, and YouTube Shorts</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="shorts-platform" class="block text-sm font-medium text-gray-900 mb-2">Platform</label>
                    <select
                        id="shorts-platform"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                    >
                        <option value="tiktok">TikTok</option>
                        <option value="reels">Instagram Reels</option>
                        <option value="shorts">YouTube Shorts</option>
                    </select>
                </div>
            </div>

            <textarea
                id="shorts-input"
                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Paste your content or topic here..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button
                onclick="generateShorts()"
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ✨ Generate Ideas
            </button>

            <div id="shorts-result" class="hidden bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="font-semibold text-green-900 mb-3">Generated Ideas:</h3>
                <div id="shorts-output" class="space-y-2"></div>
            </div>

            <div id="shorts-loading" class="hidden text-center">
                <p class="text-gray-600">⏳ Generating...</p>
            </div>

            <div id="shorts-error" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                <p id="shorts-error-msg" class="text-red-800"></p>
            </div>
        </div>

        <!-- Embeddings Tab -->
        <div id="tab-embeddings" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🔗 Embeddings</h2>
            <p class="text-gray-600">Create vector embeddings for text</p>

            <textarea id="embeddings-input" class="w-full h-28 px-4 py-3 border border-gray-300 rounded-md" placeholder="Enter text to embed..." <?php echo !$hasApiKey ? 'disabled' : ''; ?>></textarea>
            <button onclick="createEmbeddings()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">Generate Embedding</button>

            <div id="embeddings-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Embedding Result</h3>
                <pre id="embeddings-output" class="text-sm text-gray-800 overflow-auto"></pre>
            </div>
        </div>

        <!-- Images Tab -->
        <div id="tab-images" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🖼️ Image Generation</h2>
            <p class="text-gray-600">Generate images from a prompt</p>

            <input id="images-prompt" class="w-full px-4 py-3 border border-gray-300 rounded-md" placeholder="Enter image prompt..." <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
            <div class="flex gap-2 mt-2">
                <select id="images-size" class="px-3 py-2 border rounded" <?php echo !$hasApiKey ? 'disabled' : ''; ?>>
                    <option value="512x512">512x512</option>
                    <option value="1024x1024" selected>1024x1024</option>
                </select>
                <input id="images-n" type="number" min="1" max="4" value="1" class="w-24 px-3 py-2 border rounded" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
            </div>
            <button onclick="generateImages()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md mt-3">Generate Images</button>

            <div id="images-result" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mt-4"></div>
        </div>

        <!-- Audio Tab -->
        <div id="tab-audio" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🎙️ Audio Transcription</h2>
            <p class="text-gray-600">Upload an audio file to transcribe</p>

            <input id="audio-file" type="file" accept="audio/*" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
            <button onclick="transcribeAudio()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">Transcribe</button>

            <div id="audio-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Transcription</h3>
                <pre id="audio-output" class="text-sm text-gray-800"></pre>
            </div>
        </div>

        <!-- Moderation Tab -->
        <div id="tab-moderation" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🛡️ Moderation</h2>
            <p class="text-gray-600">Check content against OpenAI moderation policies</p>

            <textarea
                id="moderation-input"
                class="w-full h-28 px-4 py-3 border border-gray-300 rounded-md"
                placeholder="Paste text to moderate..."
                <?php echo !$hasApiKey ? 'disabled' : ''; ?>
            ></textarea>

            <button onclick="runModeration()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">
                🔍 Run Moderation
            </button>

            <div id="moderation-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Moderation Result</h3>
                <pre id="moderation-output" class="text-sm text-gray-800"></pre>
            </div>
        </div>

        <!-- Models Tab -->
        <div id="tab-models" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">📚 Models</h2>
            <p class="text-gray-600">List available OpenAI models</p>

            <button onclick="loadModels()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">
                📥 Load Models
            </button>

            <div id="models-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Model List</h3>
                <ul id="models-output" class="text-sm text-gray-800 list-disc ml-5"></ul>
            </div>
        </div>

        <!-- Files Tab -->
        <div id="tab-files" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">📁 Files</h2>
            <p class="text-gray-600">Upload and list files for OpenAI</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="files-purpose" class="block text-sm font-medium text-gray-900 mb-2">Purpose (optional)</label>
                    <select
                        id="files-purpose"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md"
                        <?php echo !$hasApiKey ? 'disabled' : ''; ?>
                    >
                        <option value="">Select purpose</option>
                        <option value="fine-tune">Fine-tune (.jsonl)</option>
                        <option value="assistants">Assistants</option>
                        <option value="vision">Vision</option>
                    </select>
                </div>
                <div>
                    <label for="files-upload" class="block text-sm font-medium text-gray-900 mb-2">File</label>
                    <input id="files-upload" type="file" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button onclick="uploadFile()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">
                    ⬆️ Upload File
                </button>
                <button onclick="loadFiles()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-gray-700 text-white rounded-md">
                    📥 Refresh File List
                </button>
            </div>

            <div id="files-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Files</h3>
                <div id="files-output" class="space-y-2 text-sm text-gray-800"></div>
            </div>
        </div>

        <!-- API Explorer Tab -->
        <div id="tab-explorer" class="tab-content space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">🧭 OpenAI API Explorer</h2>
            <p class="text-gray-600">Call any OpenAI endpoint (Assistants, Threads, Runs, Vector Stores, Batches, Fine-tuning, Realtime sessions, etc.)</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="explorer-method" class="block text-sm font-medium text-gray-900 mb-2">HTTP Method</label>
                    <select id="explorer-method" class="w-full px-4 py-2 border border-gray-300 rounded-md" <?php echo !$hasApiKey ? 'disabled' : ''; ?>>
                        <option value="POST">POST</option>
                        <option value="GET">GET</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="explorer-endpoint" class="block text-sm font-medium text-gray-900 mb-2">Endpoint (start with /)</label>
                    <input id="explorer-endpoint" class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="/assistants" <?php echo !$hasApiKey ? 'disabled' : ''; ?> />
                </div>
            </div>

            <div>
                <label for="explorer-payload" class="block text-sm font-medium text-gray-900 mb-2">JSON Payload</label>
                <textarea id="explorer-payload" class="w-full h-40 px-4 py-3 border border-gray-300 rounded-md font-mono text-sm" placeholder='{"name":"Content Helper","model":"gpt-4o-mini"}' <?php echo !$hasApiKey ? 'disabled' : ''; ?>></textarea>
            </div>

            <button onclick="runApiExplorer()" <?php echo !$hasApiKey ? 'disabled' : ''; ?> class="px-6 py-2 bg-blue-600 text-white rounded-md">
                🚀 Send Request
            </button>

            <div id="explorer-result" class="hidden bg-white p-4 border rounded">
                <h3 class="font-semibold">Response</h3>
                <pre id="explorer-output" class="text-sm text-gray-800 overflow-auto"></pre>
            </div>
        </div>
    </div>
</div>

<script>
// Generate Title
async function generateTitle() {
    const input = document.getElementById('title-input').value.trim();
    if (!input) {
        alert('Please enter content');
        return;
    }

    showLoading('title');
    const result = await apiCall('/generate/title', 'POST', { content: input });
    handleResponse('title', result);
}

// Generate Description
async function generateDescription() {
    const input = document.getElementById('description-input').value.trim();
    if (!input) {
        alert('Please enter content');
        return;
    }

    showLoading('description');
    const result = await apiCall('/generate/description', 'POST', { content: input });
    if (result.success) {
        handleResponse('description', result);
        document.getElementById('description-length').textContent = result.data.result.length;
    } else {
        handleResponse('description', result);
    }
}

// Generate Tags
async function generateTags() {
    const input = document.getElementById('tags-input').value.trim();
    if (!input) {
        alert('Please enter content');
        return;
    }

    showLoading('tags');
    const result = await apiCall('/generate/tags', 'POST', { content: input });
    handleResponse('tags', result);
}

// Responses API
async function sendResponses() {
    const model = document.getElementById('responses-model').value.trim();
    const systemPrompt = document.getElementById('responses-system').value.trim();
    const userPrompt = document.getElementById('responses-user').value.trim();
    const maxOutputTokens = parseInt(document.getElementById('responses-max-tokens').value, 10) || 120;

    if (!model || !userPrompt) {
        return alert('Please provide a model and a user prompt');
    }

    const input = [];
    if (systemPrompt) {
        input.push({ role: 'system', content: systemPrompt });
    }
    input.push({ role: 'user', content: userPrompt });

    document.getElementById('responses-result').classList.add('hidden');
    const result = await apiCall('/responses', 'POST', {
        model,
        input,
        max_output_tokens: maxOutputTokens,
        temperature: 0.7
    });

    if (result.success) {
        const outputText = result.data?.output_text ?? '';
        document.getElementById('responses-output').textContent = outputText || JSON.stringify(result.data, null, 2);
        document.getElementById('responses-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Responses API failed');
    }
}

// Generate Chapters
async function generateChapters() {
    const input = document.getElementById('chapters-input').value.trim();
    if (!input) {
        alert('Please enter transcript');
        return;
    }

    showLoading('chapters');
    const result = await apiCall('/generate/timestamps', 'POST', { transcript: input });
    
    if (result.success) {
        const chapters = result.data.chapters || [];
        const html = chapters.map(ch =>
            `<div class="flex justify-between items-center bg-white p-2 rounded border border-green-200">
                <span class="font-mono text-sm text-green-700">${escapeHtml(ch.timestamp)}</span>
                <span class="text-green-800">${escapeHtml(ch.title)}</span>
            </div>`
        ).join('');
        
        document.getElementById('chapters-output').innerHTML = html;
        document.getElementById('chapters-result').classList.remove('hidden');
        document.getElementById('chapters-error').classList.add('hidden');
    } else {
        document.getElementById('chapters-error-msg').textContent = result.error?.message || 'Unknown error';
        document.getElementById('chapters-error').classList.remove('hidden');
        document.getElementById('chapters-result').classList.add('hidden');
    }
    document.getElementById('chapters-loading').classList.add('hidden');
}

// Generate Shorts Ideas
async function generateShorts() {
    const input = document.getElementById('shorts-input').value.trim();
    const platform = document.getElementById('shorts-platform').value;
    
    if (!input) {
        alert('Please enter content');
        return;
    }

    showLoading('shorts');
    const result = await apiCall('/generate/shorts-ideas', 'POST', {
        content: input,
        platform: platform
    });
    
    if (result.success) {
        const ideas = result.data.ideas || [];
        const html = ideas.map((idea, idx) =>
            `<div class="bg-white p-3 rounded border border-green-200">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-green-900">${idx + 1}. ${escapeHtml(idea)}</p>
                    </div>
                    <button onclick="copyToClipboard('${escapeHtml(idea).replace(/'/g, "\\'")}'" class="text-xs text-green-600 hover:underline whitespace-nowrap">
                        📋
                    </button>
                </div>
            </div>`
        ).join('');
        
        document.getElementById('shorts-output').innerHTML = html;
        document.getElementById('shorts-result').classList.remove('hidden');
        document.getElementById('shorts-error').classList.add('hidden');
    } else {
        document.getElementById('shorts-error-msg').textContent = result.error?.message || 'Unknown error';
        document.getElementById('shorts-error').classList.remove('hidden');
        document.getElementById('shorts-result').classList.add('hidden');
    }
    document.getElementById('shorts-loading').classList.add('hidden');
}

// Helper functions
function showLoading(tab) {
    document.getElementById(tab + '-loading').classList.remove('hidden');
    document.getElementById(tab + '-result').classList.add('hidden');
    document.getElementById(tab + '-error').classList.add('hidden');
}

function handleResponse(tab, result) {
    document.getElementById(tab + '-loading').classList.add('hidden');
    
    if (result.success) {
        document.getElementById(tab + '-output').textContent = result.data.result;
        document.getElementById(tab + '-result').classList.remove('hidden');
        document.getElementById(tab + '-error').classList.add('hidden');
    } else {
        document.getElementById(tab + '-error-msg').textContent = result.error?.message || 'Unknown error';
        document.getElementById(tab + '-error').classList.remove('hidden');
        document.getElementById(tab + '-result').classList.add('hidden');
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function copyChaptersToClipboard() {
    const chapters = Array.from(document.querySelectorAll('#chapters-output > div')).map(div => {
        const time = div.querySelector('.font-mono').textContent;
        const title = Array.from(div.children).slice(1).map(el => el.textContent).join('');
        return `[${time}] ${title}`;
    }).join('\n');
    copyToClipboard(chapters);
}

// Generic API call helper
async function apiCall(path, method = 'GET', body = null, isForm = false) {
    try {
        if (isForm && body instanceof FormData) {
            const res = await fetch('/api' + path, { method, body });
            return await res.json();
        }

        const res = await fetch('/api' + path, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: body ? JSON.stringify(body) : null,
        });

        return await res.json();
    } catch (err) {
        return { success: false, error: { message: err.message || 'Network error' } };
    }
}

// Switch tab UI
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('border-b-blue-600'));
    const active = document.getElementById('tab-' + tab);
    if (active) active.classList.add('active');
}

// Copy helper
function copyToClipboard(text) {
    navigator.clipboard?.writeText(text).catch(()=>{});
}

// Embeddings
async function createEmbeddings() {
    const input = document.getElementById('embeddings-input').value.trim();
    if (!input) return alert('Please enter text to embed');

    document.getElementById('embeddings-result').classList.add('hidden');
    const result = await apiCall('/embeddings', 'POST', { input });
    if (result.success) {
        document.getElementById('embeddings-output').textContent = JSON.stringify(result.data.embeddings, null, 2);
        document.getElementById('embeddings-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Error creating embeddings');
    }
}

// Moderation
async function runModeration() {
    const input = document.getElementById('moderation-input').value.trim();
    if (!input) return alert('Please enter text to moderate');

    document.getElementById('moderation-result').classList.add('hidden');
    const result = await apiCall('/moderation', 'POST', { input });
    if (result.success) {
        document.getElementById('moderation-output').textContent = JSON.stringify(result.data, null, 2);
        document.getElementById('moderation-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Moderation failed');
    }
}

// Models
async function loadModels() {
    document.getElementById('models-result').classList.add('hidden');
    const result = await apiCall('/models', 'GET');
    if (result.success) {
        const list = document.getElementById('models-output');
        list.innerHTML = '';
        const models = result.data?.data || [];
        if (models.length === 0) {
            list.innerHTML = '<li>No models returned.</li>';
        } else {
            for (const model of models) {
                const item = document.createElement('li');
                item.textContent = model.id || 'Unknown model';
                list.appendChild(item);
            }
        }
        document.getElementById('models-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Failed to load models');
    }
}

// Files
async function loadFiles() {
    document.getElementById('files-result').classList.add('hidden');
    const result = await apiCall('/files', 'GET');
    if (result.success) {
        const container = document.getElementById('files-output');
        container.innerHTML = '';
        const files = result.data?.data || [];
        if (files.length === 0) {
            container.innerHTML = '<p>No files found.</p>';
        } else {
            for (const file of files) {
                const entry = document.createElement('div');
                entry.className = 'border rounded p-2';
                entry.innerHTML = `
                    <div class="font-semibold">${escapeHtml(file.filename || file.id || 'File')}</div>
                    <div class="text-xs text-gray-600">ID: ${escapeHtml(file.id || 'n/a')}</div>
                    <div class="text-xs text-gray-600">Purpose: ${escapeHtml(file.purpose || 'n/a')}</div>
                `;
                container.appendChild(entry);
            }
        }
        document.getElementById('files-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Failed to load files');
    }
}

async function uploadFile() {
    const fileInput = document.getElementById('files-upload');
    if (!fileInput.files || fileInput.files.length === 0) {
        return alert('Please select a file');
    }

    const form = new FormData();
    form.append('file', fileInput.files[0]);

    const purpose = document.getElementById('files-purpose').value;
    if (purpose) {
        form.append('purpose', purpose);
    }

    const result = await apiCall('/files/upload', 'POST', form, true);
    if (result.success) {
        alert('File uploaded successfully');
        await loadFiles();
    } else {
        alert(result.error?.message || 'File upload failed');
    }
}

// OpenAI API Explorer
async function runApiExplorer() {
    const method = document.getElementById('explorer-method').value;
    const endpoint = document.getElementById('explorer-endpoint').value.trim();
    const payloadRaw = document.getElementById('explorer-payload').value.trim();

    if (!endpoint) {
        return alert('Please enter an endpoint starting with "/"');
    }

    let payload = {};
    if (payloadRaw) {
        try {
            payload = JSON.parse(payloadRaw);
        } catch (err) {
            return alert('Invalid JSON payload');
        }
    }

    document.getElementById('explorer-result').classList.add('hidden');
    const result = await apiCall('/openai/request', 'POST', {
        method,
        endpoint,
        payload
    });

    if (result.success) {
        document.getElementById('explorer-output').textContent = JSON.stringify(result.data, null, 2);
        document.getElementById('explorer-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Request failed');
    }
}

// Images
async function generateImages() {
    const prompt = document.getElementById('images-prompt').value.trim();
    const size = document.getElementById('images-size').value;
    const n = parseInt(document.getElementById('images-n').value) || 1;
    if (!prompt) return alert('Please enter an image prompt');

    document.getElementById('images-result').classList.add('hidden');
    const result = await apiCall('/images/generate', 'POST', { prompt, options: { size, n } });

    if (result.success) {
        const container = document.getElementById('images-result');
        container.innerHTML = '';
        const images = result.data.images?.data || result.data.images?.data || [];
        // Some APIs return base64 in data array
        if (result.data.images && result.data.images.data) {
            for (const img of result.data.images.data) {
                const b64 = img.b64_json || img.b64 || img.base64 || null;
                const url = img.url ?? null;
                const el = document.createElement('div');
                el.className = 'bg-white p-2 rounded border';
                if (b64) {
                    const imgEl = document.createElement('img');
                    imgEl.src = 'data:image/png;base64,' + b64;
                    imgEl.className = 'w-full h-auto';
                    el.appendChild(imgEl);
                } else if (url) {
                    const imgEl = document.createElement('img');
                    imgEl.src = url;
                    imgEl.className = 'w-full h-auto';
                    el.appendChild(imgEl);
                }
                container.appendChild(el);
            }
        }
        document.getElementById('images-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Image generation failed');
    }
}

// Audio transcription
async function transcribeAudio() {
    const fileInput = document.getElementById('audio-file');
    if (!fileInput.files || fileInput.files.length === 0) return alert('Please select an audio file');

    const form = new FormData();
    form.append('file', fileInput.files[0]);

    const result = await apiCall('/audio/transcribe', 'POST', form, true);
    if (result.success) {
        document.getElementById('audio-output').textContent = JSON.stringify(result.data.transcription, null, 2);
        document.getElementById('audio-result').classList.remove('hidden');
    } else {
        alert(result.error?.message || 'Transcription failed');
    }
}
</script>
