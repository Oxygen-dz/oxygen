<?php

/**
 * CORS Configuration
 * 
 * Configure Cross-Origin Resource Sharing (CORS) settings
 * for your API to work with modern frontend frameworks.
 * 
 * @package OxygenFramework
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Specify which origins are allowed to access your API.
    | Use '*' for all origins (development only), or specify exact domains.
    | Multiple origins can be comma-separated in .env
    |
    */
    'allowed_origins' => explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: '*'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | HTTP methods that are allowed for CORS requests.
    |
    */
    'allowed_methods' => explode(',', getenv('CORS_ALLOWED_METHODS') ?: 'GET,POST,PUT,DELETE,PATCH,OPTIONS'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are allowed in CORS requests.
    |
    */
    'allowed_headers' => explode(',', getenv('CORS_ALLOWED_HEADERS') ?: 'Content-Type,Authorization,X-Requested-With,X-CSRF-Token'),

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are exposed to the browser.
    |
    */
    'exposed_headers' => explode(',', getenv('CORS_EXPOSED_HEADERS') ?: 'X-RateLimit-Limit,X-RateLimit-Remaining,X-RateLimit-Reset'),

    /*
    |--------------------------------------------------------------------------
    | Allow Credentials
    |--------------------------------------------------------------------------
    |
    | Whether to allow credentials (cookies, authorization headers, etc.)
    | in CORS requests. Set to true if your frontend needs to send cookies.
    |
    */
    'allow_credentials' => filter_var(getenv('CORS_ALLOW_CREDENTIALS') ?: 'false', FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the preflight request can be cached.
    | Default: 24 hours (86400 seconds)
    |
    */
    'max_age' => (int) (getenv('CORS_MAX_AGE') ?: 86400),
];
