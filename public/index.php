<?php

/**
 * OpenAI Content Toolkit - Main Router
 * Handles authentication, page routing, and admin actions
 */

require_once dirname(__DIR__) . '/bootstrap.php';

use OpenAI\Support\Config;
use OpenAI\Support\Csrf;
use OpenAI\Support\LoggerService;
use OpenAI\Client\OpenAIClient;

// Initialize config
Config::load();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    LoggerService::info('User logged out');
    session_destroy();
    header('Location: /');
    exit;
}

// Check authentication (except for login page)
$page = $_GET['page'] ?? 'dashboard';
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Login handling
if (!$isLoggedIn && isset($_POST['username'], $_POST['password'])) {
    // Verify CSRF token
    if (!Csrf::verifyRequest()) {
        $loginError = 'CSRF token validation failed. Please try again.';
    } else {
        // Verify credentials (default: admin/admin)
        // In production, use proper password hashing
        if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            session_regenerate_id(true);
            LoggerService::info('Admin login successful');
            header('Location: ?page=dashboard');
            exit;
        } else {
            $loginError = 'Invalid username or password';
            LoggerService::warning('Failed login attempt', ['username' => $_POST['username']]);
        }
    }
}

// Handle page requests
if ($isLoggedIn) {
    // Handle Settings page actions
    if ($page === 'settings') {
        // Handle API key test
        if (isset($_GET['test_key']) && $_GET['test_key'] === '1') {
            try {
                $client = new OpenAIClient();
                $success = $client->testConnection();
                if ($success) {
                    $_SESSION['flash']['success'] = '✅ API key is valid and working!';
                    LoggerService::info('API key test successful');
                } else {
                    $_SESSION['flash']['error'] = '❌ API key test failed. Please check your key.';
                    LoggerService::warning('API key test failed');
                }
            } catch (\Exception $e) {
                $_SESSION['flash']['error'] = '❌ API key test failed: ' . $e->getMessage();
                LoggerService::error('API key test error', ['error' => $e->getMessage()]);
            }
            header('Location: ?page=settings');
            exit;
        }

        // Handle Settings form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            // Verify CSRF token
            if (!Csrf::verifyRequest()) {
                $_SESSION['flash']['error'] = 'CSRF token validation failed';
            } elseif ($_POST['action'] === 'save') {
                try {
                    // Update config with new values
                    Config::set('openai_api_key', $_POST['openai_api_key'] ?? '');
                    Config::set('openai_model', $_POST['openai_model'] ?? 'gpt-4o-mini');
                    Config::set('openai_temperature', (float)($_POST['openai_temperature'] ?? 0.7));
                    Config::set('openai_max_output_tokens', (int)($_POST['openai_max_output_tokens'] ?? 800));
                    Config::set('openai_timeout', (int)($_POST['openai_timeout'] ?? 30));

                    // Validate
                    $errors = Config::validate();
                    if (!empty($errors)) {
                        $_SESSION['flash']['error'] = 'Configuration validation failed. Please check your settings.';
                        LoggerService::warning('Settings validation failed', ['errors' => $errors]);
                    } else {
                        Config::save();
                        $_SESSION['flash']['success'] = '✅ Settings saved successfully!';
                        LoggerService::info('Settings updated by admin');
                    }
                } catch (\Exception $e) {
                    $_SESSION['flash']['error'] = 'Error saving settings: ' . $e->getMessage();
                    LoggerService::error('Settings save error', ['error' => $e->getMessage()]);
                }

                header('Location: ?page=settings');
                exit;
            } elseif ($_POST['action'] === 'test') {
                header('Location: ?page=settings&test_key=1');
                exit;
            }
        }
    }

    // Render page
    $pageTitle = match ($page) {
        'dashboard' => 'Dashboard',
        'settings' => 'Settings',
        'playground' => 'Playground',
        'models' => 'Models',
        'files' => 'Files',
        'moderation' => 'Moderation',
        default => 'Dashboard',
    };

    $viewFile = PROJECT_ROOT . '/views/' . match ($page) {
        'dashboard' => 'dashboard.php',
        'settings' => 'settings.php',
        'playground' => 'playground.php',
        'models' => 'models.php',
        'files' => 'files.php',
        'moderation' => 'moderation.php',
        default => 'dashboard.php',
    };

    if (!file_exists($viewFile)) {
        die('View file not found: ' . $viewFile);
    }

    include PROJECT_ROOT . '/views/layout.php';
} else {
    // Not logged in - show login form
    $pageTitle = 'Login';
    $viewFile = PROJECT_ROOT . '/views/login.php';
    include PROJECT_ROOT . '/views/layout.php';
}
