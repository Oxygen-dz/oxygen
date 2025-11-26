<?php

namespace Oxygen\Core\Queue;

/**
 * OxygenQueue - Background Job Queue
 * 
 * Simple, fast queue system.
 * Simpler than Laravel's queue.
 * 
 * @package    Oxygen\Core\Queue
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenQueue
{
    protected static $driver = null;
    protected static $jobs = [];

    /**
     * Push job to queue
     */
    public static function push($job, $data = [])
    {
        $id = uniqid('job_');

        $jobData = [
            'id' => $id,
            'job' => $job,
            'data' => $data,
            'attempts' => 0,
            'created_at' => time()
        ];

        self::saveJob($jobData);

        return $id;
    }

    /**
     * Process next job
     */
    public static function work()
    {
        $job = self::getNextJob();

        if (!$job) {
            return false;
        }

        try {
            $jobClass = $job['job'];
            $instance = new $jobClass();
            $instance->handle($job['data']);

            self::deleteJob($job['id']);
            return true;
        } catch (\Exception $e) {
            $job['attempts']++;

            if ($job['attempts'] >= 3) {
                self::failJob($job);
            } else {
                self::saveJob($job);
            }

            return false;
        }
    }

    /**
     * Save job to storage
     */
    protected static function saveJob($job)
    {
        $dir = __DIR__ . '/../../../storage/queue';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents("$dir/{$job['id']}.json", json_encode($job));
    }

    /**
     * Get next job
     */
    protected static function getNextJob()
    {
        $dir = __DIR__ . '/../../../storage/queue';
        if (!is_dir($dir)) {
            return null;
        }

        $files = glob("$dir/*.json");
        if (empty($files)) {
            return null;
        }

        sort($files);
        $file = $files[0];

        return json_decode(file_get_contents($file), true);
    }

    /**
     * Delete job
     */
    protected static function deleteJob($id)
    {
        $file = __DIR__ . "/../../../storage/queue/$id.json";
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Move job to failed
     */
    protected static function failJob($job)
    {
        $dir = __DIR__ . '/../../../storage/queue/failed';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents("$dir/{$job['id']}.json", json_encode($job));
        self::deleteJob($job['id']);
    }
}

/**
 * Base Job Class
 */
abstract class Job
{
    abstract public function handle($data);
}
