<?php

namespace Oxygen\Core;

class Response
{
    protected $content;
    protected $statusCode;
    protected $headers = [];

    public function __construct($content = '', $statusCode = 200, $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function header($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        echo $this->content;
    }

    public static function json($data, $statusCode = 200)
    {
        return new static(json_encode($data), $statusCode, ['Content-Type' => 'application/json']);
    }

    /**
     * Create a standardized API success response
     * 
     * @param mixed $data Response data
     * @param string|null $message Optional message
     * @param int $statusCode HTTP status code
     * @return static
     */
    public static function apiSuccess($data = null, $message = null, $statusCode = 200)
    {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        $response['data'] = $data;
        $response['timestamp'] = date('c');

        return static::json($response, $statusCode);
    }

    /**
     * Create a standardized API error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $errors Additional error details
     * @return static
     */
    public static function apiError($message, $statusCode = 400, $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('c')
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return static::json($response, $statusCode);
    }

    /**
     * Create a paginated API response
     * 
     * @param array $items Items to paginate
     * @param int $total Total number of items
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return static
     */
    public static function apiPaginated($items, $total, $page = 1, $perPage = 15)
    {
        $lastPage = ceil($total / $perPage);

        $response = [
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => (int) $total,
                'last_page' => (int) $lastPage,
                'from' => (($page - 1) * $perPage) + 1,
                'to' => min($page * $perPage, $total),
            ],
            'links' => [
                'first' => '?page=1',
                'last' => '?page=' . $lastPage,
                'prev' => $page > 1 ? '?page=' . ($page - 1) : null,
                'next' => $page < $lastPage ? '?page=' . ($page + 1) : null,
            ],
            'timestamp' => date('c')
        ];

        return static::json($response, 200);
    }
}
