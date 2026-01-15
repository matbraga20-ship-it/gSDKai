<?php

namespace OpenAI\Support;

use OpenAI\Exceptions\RateLimitException;

/**
 * Rate Limiter
 * File-based rate limiting for API requests (30 requests per minute per IP)
 */
class RateLimiter
{
    private const LIMIT_PER_MINUTE = 30;
    private const WINDOW_SECONDS = 60;

    /**
     * Check rate limit for current IP
     */
    public static function check(): void
    {
        $ip = self::getClientIp();
        $file = self::getRateLimitFile($ip);
        $now = time();

        // Read existing entries
        $entries = [];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (!empty($content)) {
                $entries = json_decode($content, true) ?? [];
            }
        }

        // Remove expired entries (older than WINDOW_SECONDS)
        $entries = array_filter($entries, function ($timestamp) use ($now) {
            return ($now - $timestamp) < self::WINDOW_SECONDS;
        });

        // Check if limit exceeded
        if (count($entries) >= self::LIMIT_PER_MINUTE) {
            LoggerService::warning('Rate limit exceeded for IP: ' . $ip);
            throw new RateLimitException(
                'Rate limit exceeded: ' . self::LIMIT_PER_MINUTE . ' requests per minute',
                self::WINDOW_SECONDS
            );
        }

        // Add current request
        $entries[] = $now;

        // Save updated entries
        $json = json_encode(array_values($entries));
        file_put_contents($file, $json, LOCK_EX);

        // Cleanup old files periodically (5% chance per request)
        if (random_int(1, 100) <= 5) {
            self::cleanup();
        }
    }

    /**
     * Get client IP address
     */
    private static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Use first IP if multiple (X-Forwarded-For can contain comma-separated IPs)
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }

        // Validate IP
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return 'unknown';
    }

    /**
     * Get rate limit file for IP
     */
    private static function getRateLimitFile(string $ip): string
    {
        $hash = md5($ip);
        return STORAGE_CACHE_PATH . '/rate_limit_' . $hash . '.json';
    }

    /**
     * Get current request count for IP
     */
    public static function getCount(): int
    {
        $ip = self::getClientIp();
        $file = self::getRateLimitFile($ip);
        $now = time();

        if (!file_exists($file)) {
            return 0;
        }

        $content = file_get_contents($file);
        $entries = json_decode($content, true) ?? [];

        // Count only entries within current window
        return count(array_filter($entries, function ($timestamp) use ($now) {
            return ($now - $timestamp) < self::WINDOW_SECONDS;
        }));
    }

    /**
     * Get remaining requests for IP
     */
    public static function getRemaining(): int
    {
        return max(0, self::LIMIT_PER_MINUTE - self::getCount());
    }

    /**
     * Get reset time (Unix timestamp)
     */
    public static function getResetTime(): int
    {
        $ip = self::getClientIp();
        $file = self::getRateLimitFile($ip);

        if (!file_exists($file)) {
            return time() + self::WINDOW_SECONDS;
        }

        $content = file_get_contents($file);
        $entries = json_decode($content, true) ?? [];

        if (empty($entries)) {
            return time() + self::WINDOW_SECONDS;
        }

        // Find oldest entry
        $oldestTime = min($entries);
        return $oldestTime + self::WINDOW_SECONDS;
    }

    /**
     * Cleanup old rate limit files
     */
    private static function cleanup(): void
    {
        if (!is_dir(STORAGE_CACHE_PATH)) {
            return;
        }

        $files = glob(STORAGE_CACHE_PATH . '/rate_limit_*.json');
        $now = time();
        $maxAge = 86400; // Keep for 24 hours

        foreach ($files as $file) {
            if ((time() - filemtime($file)) > $maxAge) {
                @unlink($file);
            }
        }
    }

    /**
     * Reset rate limit for IP (admin only)
     */
    public static function reset(string $ip): void
    {
        $file = self::getRateLimitFile($ip);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
