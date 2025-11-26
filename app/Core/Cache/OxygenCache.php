<?php

namespace Oxygen\Core\Cache;

/**
 * OxygenCache - High-Performance Caching System
 * 
 * Faster and simpler than Laravel's cache.
 * Supports: File, Redis, Memcached, APCu
 * 
 * @package    Oxygen\Core\Cache
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenCache
{
    protected static $driver = null;
    protected static $config = [];

    /**
     * Initialize cache system
     */
    public static function init()
    {
        if (self::$driver !== null) {
            return;
        }

        $driver = $_ENV['CACHE_DRIVER'] ?? 'file';

        switch ($driver) {
            case 'redis':
                self::$driver = new RedisDriver();
                break;
            case 'memcached':
                self::$driver = new MemcachedDriver();
                break;
            case 'apcu':
                self::$driver = new ApcuDriver();
                break;
            default:
                self::$driver = new FileDriver();
        }
    }

    /**
     * Get cached value
     * 
     * @param string $key Cache key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        self::init();
        $value = self::$driver->get($key);
        return $value !== null ? $value : $default;
    }

    /**
     * Store value in cache
     * 
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int $ttl Time to live in seconds (0 = forever)
     * @return bool
     */
    public static function put($key, $value, $ttl = 3600)
    {
        self::init();
        return self::$driver->put($key, $value, $ttl);
    }

    /**
     * Store value forever
     */
    public static function forever($key, $value)
    {
        return self::put($key, $value, 0);
    }

    /**
     * Check if key exists
     */
    public static function has($key)
    {
        self::init();
        return self::$driver->has($key);
    }

    /**
     * Delete cached value
     */
    public static function forget($key)
    {
        self::init();
        return self::$driver->forget($key);
    }

    /**
     * Clear all cache
     */
    public static function flush()
    {
        self::init();
        return self::$driver->flush();
    }

    /**
     * Remember - Get or store
     * 
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @return mixed
     */
    public static function remember($key, $ttl, $callback)
    {
        if (self::has($key)) {
            return self::get($key);
        }

        $value = $callback();
        self::put($key, $value, $ttl);

        return $value;
    }

    /**
     * Increment value
     */
    public static function increment($key, $value = 1)
    {
        self::init();
        return self::$driver->increment($key, $value);
    }

    /**
     * Decrement value
     */
    public static function decrement($key, $value = 1)
    {
        self::init();
        return self::$driver->decrement($key, $value);
    }
}

/**
 * File Cache Driver
 */
class FileDriver
{
    protected $path;

    public function __construct()
    {
        $this->path = __DIR__ . '/../../../storage/cache';
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function get($key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires'] > 0 && $data['expires'] < time()) {
            $this->forget($key);
            return null;
        }

        return $data['value'];
    }

    public function put($key, $value, $ttl)
    {
        $file = $this->getFilePath($key);
        $data = [
            'value' => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function has($key)
    {
        return $this->get($key) !== null;
    }

    public function forget($key)
    {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : false;
    }

    public function flush()
    {
        $files = glob($this->path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function increment($key, $value)
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->put($key, $new, 3600);
        return $new;
    }

    public function decrement($key, $value)
    {
        return $this->increment($key, -$value);
    }

    protected function getFilePath($key)
    {
        return $this->path . '/' . md5($key) . '.cache';
    }
}

/**
 * Redis Cache Driver
 */
class RedisDriver
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(
            $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            $_ENV['REDIS_PORT'] ?? 6379
        );
    }

    public function get($key)
    {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : null;
    }

    public function put($key, $value, $ttl)
    {
        if ($ttl > 0) {
            return $this->redis->setex($key, $ttl, serialize($value));
        }
        return $this->redis->set($key, serialize($value));
    }

    public function has($key)
    {
        return $this->redis->exists($key);
    }

    public function forget($key)
    {
        return $this->redis->del($key) > 0;
    }

    public function flush()
    {
        return $this->redis->flushDB();
    }

    public function increment($key, $value)
    {
        return $this->redis->incrBy($key, $value);
    }

    public function decrement($key, $value)
    {
        return $this->redis->decrBy($key, $value);
    }
}

/**
 * APCu Cache Driver
 */
class ApcuDriver
{
    public function get($key)
    {
        $value = apcu_fetch($key, $success);
        return $success ? $value : null;
    }

    public function put($key, $value, $ttl)
    {
        return apcu_store($key, $value, $ttl);
    }

    public function has($key)
    {
        return apcu_exists($key);
    }

    public function forget($key)
    {
        return apcu_delete($key);
    }

    public function flush()
    {
        return apcu_clear_cache();
    }

    public function increment($key, $value)
    {
        return apcu_inc($key, $value);
    }

    public function decrement($key, $value)
    {
        return apcu_dec($key, $value);
    }
}
