<?php

namespace Oxygen\Services;

use Oxygen\Core\Security\OxygenSecurity;

/**
 * OxygenStorageService - File Storage Service
 * 
 * Professional file upload and storage management.
 * 
 * @package    Oxygen\Services
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenStorageService
{
    protected $basePath;
    protected $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    protected $maxSize = 5242880; // 5MB

    public function __construct()
    {
        $this->basePath = __DIR__ . '/../../public/storage/';
    }

    /**
     * Upload a file
     * 
     * @param array $file $_FILES array element
     * @param string $directory Target directory
     * @param array $options Upload options
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function upload($file, $directory = 'uploads', $options = [])
    {
        // Validate file
        $validation = OxygenSecurity::validateFileUpload(
            $file,
            $options['allowed_types'] ?? $this->allowedTypes,
            $options['max_size'] ?? $this->maxSize
        );

        if (!$validation['valid']) {
            return [
                'success' => false,
                'path' => null,
                'error' => $validation['error']
            ];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;

        // Create directory if it doesn't exist
        $targetDir = $this->basePath . $directory;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Move uploaded file
        $targetPath = $targetDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => true,
                'path' => $directory . '/' . $filename,
                'error' => null
            ];
        }

        return [
            'success' => false,
            'path' => null,
            'error' => 'Failed to move uploaded file'
        ];
    }

    /**
     * Delete a file
     * 
     * @param string $path File path relative to storage
     * @return bool
     */
    public function delete($path)
    {
        $fullPath = $this->basePath . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Get file URL
     * 
     * @param string $path File path
     * @return string
     */
    public function url($path)
    {
        $appUrl = $_ENV['APP_URL'] ?? '';
        return $appUrl . '/public/storage/' . $path;
    }

    /**
     * Check if file exists
     * 
     * @param string $path File path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($this->basePath . $path);
    }
}
