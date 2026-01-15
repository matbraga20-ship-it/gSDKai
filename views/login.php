<?php
/**
 * Login page view
 */
?>
<div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">🚀 OpenAI Toolkit</h2>
        <p class="text-gray-600 mt-2">Admin Login</p>
    </div>

    <?php if (isset($loginError)): ?>
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <p class="text-sm text-red-800"><?php echo htmlspecialchars($loginError); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="?action=login" class="space-y-4">
        <?php echo OpenAI\Support\Csrf::field(); ?>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="admin"
                required
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="••••••••"
                required
            >
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition"
        >
            Sign In
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-600 text-center">
            <strong>Demo Credentials:</strong><br>
            Username: <code class="bg-gray-100 px-1">admin</code><br>
            Password: <code class="bg-gray-100 px-1">admin</code><br>
            <em class="block mt-2 text-gray-500">⚠️ Please change password in Settings</em>
        </p>
    </div>
</div>
