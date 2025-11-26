<?php

namespace Oxygen\Core\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

/**
 * OxygenJWT - JSON Web Token Handler
 * 
 * Handles JWT token generation, validation, and management
 * for API authentication.
 * 
 * @package    Oxygen\Core\Auth
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenJWT
{
    /**
     * Generate a JWT token for a user
     * 
     * @param array $payload User data to encode in token
     * @param bool $isRefreshToken Whether this is a refresh token
     * @return string JWT token
     */
    public static function generate($payload, $isRefreshToken = false)
    {
        $config = require __DIR__ . '/../../../config/jwt.php';

        $now = time();
        $expiration = $isRefreshToken
            ? $config['refresh_expiration']
            : $config['expiration'];

        $tokenPayload = [
            'iss' => $config['issuer'],           // Issuer
            'aud' => $config['audience'],         // Audience
            'iat' => $now,                        // Issued at
            'nbf' => $now,                        // Not before
            'exp' => $now + $expiration,          // Expiration
            'jti' => bin2hex(random_bytes(16)),   // JWT ID (unique identifier)
            'data' => $payload,                   // User data
            'type' => $isRefreshToken ? 'refresh' : 'access'
        ];

        return JWT::encode($tokenPayload, $config['secret'], $config['algorithm']);
    }

    /**
     * Validate and decode a JWT token
     * 
     * @param string $token JWT token to validate
     * @return object|null Decoded token payload or null if invalid
     */
    public static function validate($token)
    {
        try {
            $config = require __DIR__ . '/../../../config/jwt.php';

            // Check if token is blacklisted
            if ($config['blacklist_enabled'] && static::isBlacklisted($token)) {
                return null;
            }

            $decoded = JWT::decode($token, new Key($config['secret'], $config['algorithm']));

            // Verify issuer and audience
            if ($decoded->iss !== $config['issuer'] || $decoded->aud !== $config['audience']) {
                return null;
            }

            return $decoded;
        } catch (Exception $e) {
            // Token is invalid, expired, or malformed
            return null;
        }
    }

    /**
     * Extract token from Authorization header
     * 
     * @param string|null $authHeader Authorization header value
     * @return string|null Token or null if not found
     */
    public static function extractFromHeader($authHeader)
    {
        if (!$authHeader) {
            return null;
        }

        // Format: "Bearer {token}"
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Refresh an access token using a refresh token
     * 
     * @param string $refreshToken Refresh token
     * @return array|null New access and refresh tokens or null if invalid
     */
    public static function refresh($refreshToken)
    {
        $decoded = static::validate($refreshToken);

        if (!$decoded || $decoded->type !== 'refresh') {
            return null;
        }

        // Generate new tokens
        return [
            'access_token' => static::generate($decoded->data, false),
            'refresh_token' => static::generate($decoded->data, true),
            'token_type' => 'Bearer',
            'expires_in' => (require __DIR__ . '/../../../config/jwt.php')['expiration']
        ];
    }

    /**
     * Blacklist a token (for logout)
     * 
     * @param string $token Token to blacklist
     * @return bool Success
     */
    public static function blacklist($token)
    {
        $config = require __DIR__ . '/../../../config/jwt.php';

        if (!$config['blacklist_enabled']) {
            return true;
        }

        try {
            $decoded = JWT::decode($token, new Key($config['secret'], $config['algorithm']));

            $blacklistDir = __DIR__ . '/../../../storage/framework/jwt-blacklist';
            if (!is_dir($blacklistDir)) {
                mkdir($blacklistDir, 0755, true);
            }

            $filename = $blacklistDir . '/' . md5($token) . '.txt';
            file_put_contents($filename, $decoded->exp);

            // Clean up expired blacklisted tokens
            static::cleanupBlacklist();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if a token is blacklisted
     * 
     * @param string $token Token to check
     * @return bool True if blacklisted
     */
    public static function isBlacklisted($token)
    {
        $blacklistDir = __DIR__ . '/../../../storage/framework/jwt-blacklist';
        $filename = $blacklistDir . '/' . md5($token) . '.txt';

        if (!file_exists($filename)) {
            return false;
        }

        $expiration = (int) file_get_contents($filename);

        // If token has expired, remove from blacklist
        if (time() > $expiration) {
            @unlink($filename);
            return false;
        }

        return true;
    }

    /**
     * Clean up expired tokens from blacklist
     * 
     * @return int Number of tokens removed
     */
    protected static function cleanupBlacklist()
    {
        $blacklistDir = __DIR__ . '/../../../storage/framework/jwt-blacklist';

        if (!is_dir($blacklistDir)) {
            return 0;
        }

        $removed = 0;
        $now = time();

        foreach (glob($blacklistDir . '/*.txt') as $file) {
            $expiration = (int) file_get_contents($file);
            if ($now > $expiration) {
                @unlink($file);
                $removed++;
            }
        }

        return $removed;
    }

    /**
     * Get token payload without validation (for debugging)
     * 
     * @param string $token JWT token
     * @return object|null Decoded payload or null
     */
    public static function decode($token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }

            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')));
            return $payload;
        } catch (Exception $e) {
            return null;
        }
    }
}
