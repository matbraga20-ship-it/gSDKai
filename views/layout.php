<?php
/**
 * Main layout template
 */

// Check if user is logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$currentPage = $currentPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI Content Toolkit <?php echo $pageTitle ? '- ' . $pageTitle : ''; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [data-theme="dark"] { color-scheme: dark; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-50">
    <?php if ($isLoggedIn): ?>
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-8">
                        <h1 class="text-2xl font-bold text-gray-900">🚀 OpenAI Toolkit</h1>
                        <div class="hidden md:flex space-x-1">
                            <a href="?page=dashboard" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'dashboard' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Dashboard
                            </a>
                            <a href="?page=settings" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'settings' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Settings
                            </a>
                            <a href="?page=playground" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'playground' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Playground
                            </a>
                            <a href="?page=models" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'models' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Models
                            </a>
                            <a href="?page=files" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'files' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Files
                            </a>
                            <a href="?page=moderation" class="px-3 py-2 rounded-md text-sm font-medium <?php echo $currentPage === 'moderation' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                Moderation
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, Admin</span>
                        <a href="?action=logout" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="rounded-md p-4 mb-4 <?php
                        echo match($type) {
                            'success' => 'bg-green-50 text-green-800 border border-green-200',
                            'error' => 'bg-red-50 text-red-800 border border-red-200',
                            'warning' => 'bg-yellow-50 text-yellow-800 border border-yellow-200',
                            default => 'bg-blue-50 text-blue-800 border border-blue-200',
                        };
                    ?>">
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto py-8 px-4">
            <?php include $viewFile; ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <p class="text-sm text-gray-600">
                    OpenAI Content Toolkit © 2026. Powered by <a href="https://openai.com" class="text-blue-600 hover:underline">OpenAI</a>.
                </p>
            </div>
        </footer>
    <?php else: ?>
        <!-- Login Page (no navigation) -->
        <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-blue-800">
            <?php include $viewFile; ?>
        </main>
    <?php endif; ?>

    <script>
        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('[data-tab]').forEach(el => el.classList.remove('bg-blue-50', 'text-blue-700'));

            document.getElementById('tab-' + tabName).classList.add('active');
            document.querySelector('[data-tab="' + tabName + '"]').classList.add('bg-blue-50', 'text-blue-700');
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            });
        }

        // API call wrapper
        async function apiCall(endpoint, method = 'POST', data = {}) {
            try {
                const response = await fetch('/api' + endpoint, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: method === 'POST' ? JSON.stringify(data) : undefined
                });

                const result = await response.json();
                return result;
            } catch (error) {
                console.error('API Error:', error);
                return { success: false, error: { message: error.message } };
            }
        }
    </script>
</body>
</html>
