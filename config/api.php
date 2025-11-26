<?php

/**
 * API Configuration
 * 
 * Configure API settings including versioning, rate limiting,
 * pagination, and response formats.
 * 
 * @package OxygenFramework
 */

return [
    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The current version of your API. This is used in the URL prefix.
    | Example: /api/v1/users
    |
    */
    'version' => getenv('API_VERSION') ?: 'v1',

    /*
    |--------------------------------------------------------------------------
    | API Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for all API routes.
    | Default: /api
    |
    */
    'prefix' => getenv('API_PREFIX') ?: '/api',

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API endpoints.
    |
    */
    'rate_limit' => [
        'enabled' => filter_var(getenv('RATE_LIMIT_ENABLED') ?: 'true', FILTER_VALIDATE_BOOLEAN),

        // Maximum requests per window
        'max_requests' => (int) (getenv('RATE_LIMIT_MAX_REQUESTS') ?: 60),

        // Time window in seconds
        'window' => (int) (getenv('RATE_LIMIT_WINDOW') ?: 60),

        // Different limits for authenticated users
        'authenticated_max_requests' => (int) (getenv('RATE_LIMIT_AUTH_MAX_REQUESTS') ?: 120),

        // Storage driver: file, redis, database
        'driver' => getenv('RATE_LIMIT_DRIVER') ?: 'file',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for API responses.
    |
    */
    'pagination' => [
        // Default number of items per page
        'per_page' => (int) (getenv('API_PAGINATION_PER_PAGE') ?: 15),

        // Maximum items per page (to prevent abuse)
        'max_per_page' => (int) (getenv('API_PAGINATION_MAX_PER_PAGE') ?: 100),

        // Query parameter name for page number
        'page_param' => 'page',

        // Query parameter name for items per page
        'per_page_param' => 'per_page',
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Format
    |--------------------------------------------------------------------------
    |
    | Standardize API response structure.
    |
    */
    'response' => [
        // Include timestamp in responses
        'include_timestamp' => filter_var(getenv('API_INCLUDE_TIMESTAMP') ?: 'true', FILTER_VALIDATE_BOOLEAN),

        // Include request ID for tracking
        'include_request_id' => filter_var(getenv('API_INCLUDE_REQUEST_ID') ?: 'true', FILTER_VALIDATE_BOOLEAN),

        // Wrap data in 'data' key
        'wrap_data' => filter_var(getenv('API_WRAP_DATA') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Configure error response behavior.
    |
    */
    'errors' => [
        // Show detailed error messages (disable in production)
        'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN),

        // Include stack trace in error responses (development only)
        'include_trace' => filter_var(getenv('API_INCLUDE_TRACE') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | API validation settings.
    |
    */
    'validation' => [
        // Return all validation errors or stop at first error
        'return_all_errors' => filter_var(getenv('API_RETURN_ALL_ERRORS') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    ],
];
