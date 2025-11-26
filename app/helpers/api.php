<?php

/**
 * API Helper Functions
 * 
 * Quick helper functions for API responses, validation, and common tasks.
 * 
 * @package OxygenFramework
 */

use Oxygen\Core\Response;

if (!function_exists('api_response')) {
    /**
     * Create a standardized API success response
     * 
     * @param mixed $data Response data
     * @param string $message Optional message
     * @param int $statusCode HTTP status code
     * @return Response
     */
    function api_response($data = null, $message = null, $statusCode = 200)
    {
        $config = require __DIR__ . '/../../config/api.php';

        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($config['response']['wrap_data']) {
            $response['data'] = $data;
        } else {
            $response = array_merge($response, is_array($data) ? $data : ['data' => $data]);
        }

        if ($config['response']['include_timestamp']) {
            $response['timestamp'] = date('c');
        }

        if ($config['response']['include_request_id']) {
            $response['request_id'] = uniqid('req_', true);
        }

        return Response::json($response, $statusCode);
    }
}

if (!function_exists('api_error')) {
    /**
     * Create a standardized API error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $errors Additional error details
     * @return Response
     */
    function api_error($message, $statusCode = 400, $errors = [])
    {
        $config = require __DIR__ . '/../../config/api.php';

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if ($config['response']['include_timestamp']) {
            $response['timestamp'] = date('c');
        }

        if ($config['response']['include_request_id']) {
            $response['request_id'] = uniqid('req_', true);
        }

        // Include trace in development mode
        if ($config['errors']['include_trace'] && $config['errors']['debug']) {
            $response['trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        return Response::json($response, $statusCode);
    }
}

if (!function_exists('api_paginate')) {
    /**
     * Create a paginated API response
     * 
     * @param array $items Items to paginate
     * @param int $total Total number of items
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return Response
     */
    function api_paginate($items, $total, $page = 1, $perPage = 15)
    {
        $config = require __DIR__ . '/../../config/api.php';

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
            ]
        ];

        if ($config['response']['include_timestamp']) {
            $response['timestamp'] = date('c');
        }

        return Response::json($response, 200);
    }
}

if (!function_exists('api_validate')) {
    /**
     * Validate request data and return errors in API format
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array|null Validation errors or null if valid
     */
    function api_validate($data, $rules)
    {
        // Simple validation implementation
        // You can integrate with OxygenValidator if needed
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (!isset($data[$field]) || empty($data[$field])) {
                            $errors[$field][] = "The {$field} field is required.";
                        }
                        break;

                    case 'email':
                        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "The {$field} must be a valid email address.";
                        }
                        break;

                    case 'min':
                        if (isset($data[$field]) && strlen($data[$field]) < $ruleValue) {
                            $errors[$field][] = "The {$field} must be at least {$ruleValue} characters.";
                        }
                        break;

                    case 'max':
                        if (isset($data[$field]) && strlen($data[$field]) > $ruleValue) {
                            $errors[$field][] = "The {$field} must not exceed {$ruleValue} characters.";
                        }
                        break;

                    case 'numeric':
                        if (isset($data[$field]) && !is_numeric($data[$field])) {
                            $errors[$field][] = "The {$field} must be a number.";
                        }
                        break;
                }
            }
        }

        return empty($errors) ? null : $errors;
    }
}

if (!function_exists('cors_headers')) {
    /**
     * Get CORS headers array
     * 
     * @return array CORS headers
     */
    function cors_headers()
    {
        $config = require __DIR__ . '/../../config/cors.php';

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

        // Check if origin is allowed
        if ($config['allowed_origins'][0] !== '*') {
            if (!in_array($origin, $config['allowed_origins'])) {
                $origin = $config['allowed_origins'][0] ?? '*';
            }
        }

        return [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => implode(', ', $config['allowed_methods']),
            'Access-Control-Allow-Headers' => implode(', ', $config['allowed_headers']),
            'Access-Control-Expose-Headers' => implode(', ', $config['exposed_headers']),
            'Access-Control-Allow-Credentials' => $config['allow_credentials'] ? 'true' : 'false',
            'Access-Control-Max-Age' => (string) $config['max_age'],
        ];
    }
}
