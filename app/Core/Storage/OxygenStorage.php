<?php

namespace Oxygen\Core\Storage;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * OxygenStorage - Professional File Storage System
 * 
 * Supports local storage (public/storage) and AWS S3.
 * 
 * @package    Oxygen\Core\Storage
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenStorage
{
    protected static $disk = 'local';
    protected static $s3Client = null;
    protected static $basePath = null;

    /**
     * Initialize storage system
     */
    public static function init()
    {
        // All storage is in public/storage/
        self::$basePath = __DIR__ . '/../../../public/storage';
        self::$disk = $_ENV['STORAGE_DISK'] ?? 'local';

        // Create storage directory if it doesn't exist
        if (!is_dir(self::$basePath)) {
            mkdir(self::$basePath, 0755, true);
        }

        // Initialize S3 if needed
        if (self::$disk === 's3') {
            self::initS3();
        }
    }

    /**
     * Initialize AWS S3 client
     */
    protected static function initS3()
    {
        self::$s3Client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1',
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
            ],
        ]);
    }

    /**
     * Set storage disk
     */
    public static function disk($disk)
    {
        self::$disk = $disk;
        if ($disk === 's3' && self::$s3Client === null) {
            self::initS3();
        }
        return new static();
    }

    /**
     * Store a file
     */
    public static function put($file, $path = '', $name = null)
    {
        self::init();

        // Handle uploaded file
        if (is_array($file) && isset($file['tmp_name'])) {
            $sourcePath = $file['tmp_name'];
            $originalName = $file['name'];
        } else {
            $sourcePath = $file;
            $originalName = basename($file);
        }

        // Generate filename
        $filename = $name ?? uniqid() . '_' . $originalName;
        $fullPath = trim($path, '/') . '/' . $filename;

        // Validate file type
        if (!self::validateFile($sourcePath)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }

        if (self::$disk === 's3') {
            return self::putS3($sourcePath, $fullPath);
        } else {
            return self::putLocal($sourcePath, $fullPath);
        }
    }

    /**
     * Validate file type to prevent PHP execution
     */
    protected static function validateFile($path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        // Block PHP files
        $blockedMimes = [
            'application/x-httpd-php',
            'application/x-php',
            'text/php',
            'text/x-php',
            'application/php'
        ];

        if (in_array($mime, $blockedMimes)) {
            return false;
        }

        // Block by extension
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bat'])) {
            return false;
        }

        return true;
    }

    /**
     * Store file locally in public/storage
     */
    protected static function putLocal($source, $path)
    {
        $destination = self::$basePath . '/' . $path;
        $dir = dirname($destination);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (copy($source, $destination)) {
            return [
                'success' => true,
                'path' => $path,
                'url' => self::url($path)
            ];
        }

        return ['success' => false, 'path' => null, 'url' => null];
    }

    /**
     * Store file on S3
     */
    protected static function putS3($source, $path)
    {
        try {
            $result = self::$s3Client->putObject([
                'Bucket' => $_ENV['AWS_BUCKET'] ?? '',
                'Key' => $path,
                'SourceFile' => $source,
                'ACL' => 'public-read',
            ]);

            return [
                'success' => true,
                'path' => $path,
                'url' => $result['ObjectURL']
            ];
        } catch (AwsException $e) {
            return ['success' => false, 'path' => null, 'url' => null];
        }
    }

    /**
     * Get file URL
     */
    public static function url($path)
    {
        self::init();

        if (self::$disk === 's3') {
            $bucket = $_ENV['AWS_BUCKET'] ?? '';
            $region = $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1';
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
        }

        $appUrl = $_ENV['APP_URL'] ?? '';
        return $appUrl . '/storage/' . ltrim($path, '/');
    }

    /**
     * Check if file exists
     */
    public static function exists($path)
    {
        self::init();

        if (self::$disk === 's3') {
            return self::$s3Client->doesObjectExist(
                $_ENV['AWS_BUCKET'] ?? '',
                $path
            );
        }

        return file_exists(self::$basePath . '/' . $path);
    }

    /**
     * Delete a file
     */
    public static function delete($path)
    {
        self::init();

        if (self::$disk === 's3') {
            try {
                self::$s3Client->deleteObject([
                    'Bucket' => $_ENV['AWS_BUCKET'] ?? '',
                    'Key' => $path,
                ]);
                return true;
            } catch (AwsException $e) {
                return false;
            }
        }

        $fullPath = self::$basePath . '/' . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Get file contents
     */
    public static function get($path)
    {
        self::init();

        if (self::$disk === 's3') {
            try {
                $result = self::$s3Client->getObject([
                    'Bucket' => $_ENV['AWS_BUCKET'] ?? '',
                    'Key' => $path,
                ]);
                return $result['Body'];
            } catch (AwsException $e) {
                return false;
            }
        }

        $fullPath = self::$basePath . '/' . $path;
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }

        return false;
    }

    /**
     * List files in directory
     */
    public static function files($directory = '')
    {
        self::init();

        if (self::$disk === 's3') {
            try {
                $result = self::$s3Client->listObjects([
                    'Bucket' => $_ENV['AWS_BUCKET'] ?? '',
                    'Prefix' => $directory,
                ]);

                $files = [];
                if (isset($result['Contents'])) {
                    foreach ($result['Contents'] as $object) {
                        $files[] = $object['Key'];
                    }
                }
                return $files;
            } catch (AwsException $e) {
                return [];
            }
        }

        $fullPath = self::$basePath . '/' . $directory;
        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];
        $items = scandir($fullPath);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_file($fullPath . '/' . $item)) {
                $files[] = $directory . '/' . $item;
            }
        }

        return $files;
    }
}
