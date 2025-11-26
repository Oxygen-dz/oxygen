<?php

namespace Oxygen\Core\Security;

/**
 * OxygenSecurity - Comprehensive Security Helpers
 * 
 * Provides security utilities for input sanitization, XSS protection,
 * SQL injection prevention, and more.
 * 
 * Compatible with PHP 7.4 - 8.4
 * 
 * @package    Oxygen\Core\Security
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenSecurity
{
    /**
     * Sanitize string input (remove HTML tags, trim whitespace)
     * 
     * @param string $input Input string
     * @return string
     */
    public static function sanitizeString($input)
    {
        return trim(strip_tags($input));
    }

    /**
     * Sanitize email address
     * 
     * @param string $email Email address
     * @return string|false
     */
    public static function sanitizeEmail($email)
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     * 
     * @param string $url URL
     * @return string|false
     */
    public static function sanitizeUrl($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Escape HTML to prevent XSS
     * 
     * @param string $string String to escape
     * @return string
     */
    public static function escapeHtml($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape for JavaScript context
     * 
     * @param string $string String to escape
     * @return string
     */
    public static function escapeJs($string)
    {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Generate a secure random token
     * 
     * @param int $length Token length
     * @return string
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Hash a password securely
     * 
     * @param string $password Password to hash
     * @return string
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify a password against a hash
     * 
     * @param string $password Password to verify
     * @param string $hash Hash to verify against
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize array of inputs
     * 
     * @param array $data Array of data
     * @return array
     */
    public static function sanitizeArray($data)
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = static::sanitizeArray($value);
            } else {
                $sanitized[$key] = static::sanitizeString($value);
            }
        }
        return $sanitized;
    }

    /**
     * Check if string contains SQL injection patterns
     * 
     * @param string $input Input to check
     * @return bool True if potentially dangerous
     */
    public static function detectSQLInjection($input)
    {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(--|\#|\/\*)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate and sanitize file upload
     * 
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Max file size in bytes
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880)
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['valid' => false, 'error' => 'Invalid file upload'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload error: ' . $file['error']];
        }

        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File too large'];
        }

        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                return ['valid' => false, 'error' => 'Invalid file type'];
            }
        }

        return ['valid' => true, 'error' => null];
    }
}
