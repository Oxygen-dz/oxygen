<?php

/**
 * JWT Configuration
 * 
 * Configure JSON Web Token (JWT) settings for API authentication.
 * 
 * @package OxygenFramework
 */

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign JWT tokens. This MUST be a strong,
    | random string. NEVER commit this to version control.
    | Generate with: php -r "echo bin2hex(random_bytes(32));"
    |
    */
    'secret' => getenv('JWT_SECRET') ?: 'CHANGE_THIS_TO_A_SECURE_RANDOM_STRING',

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the access token is valid.
    | Default: 3600 seconds (1 hour)
    |
    */
    'expiration' => (int) (getenv('JWT_EXPIRATION') ?: 3600),

    /*
    |--------------------------------------------------------------------------
    | Refresh Token Expiration
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the refresh token is valid.
    | Default: 604800 seconds (7 days)
    |
    */
    'refresh_expiration' => (int) (getenv('JWT_REFRESH_EXPIRATION') ?: 604800),

    /*
    |--------------------------------------------------------------------------
    | Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm used to sign the token.
    | Supported: HS256, HS384, HS512, RS256, RS384, RS512
    |
    */
    'algorithm' => getenv('JWT_ALGORITHM') ?: 'HS256',

    /*
    |--------------------------------------------------------------------------
    | Issuer
    |--------------------------------------------------------------------------
    |
    | The issuer of the token (usually your application name or domain).
    |
    */
    'issuer' => getenv('JWT_ISSUER') ?: getenv('APP_URL') ?: 'http://localhost',

    /*
    |--------------------------------------------------------------------------
    | Audience
    |--------------------------------------------------------------------------
    |
    | The audience of the token (who the token is intended for).
    |
    */
    'audience' => getenv('JWT_AUDIENCE') ?: getenv('APP_URL') ?: 'http://localhost',

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | Enable token blacklisting for logout functionality.
    | When enabled, logged out tokens are stored and rejected.
    |
    */
    'blacklist_enabled' => filter_var(getenv('JWT_BLACKLIST_ENABLED') ?: 'true', FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | Grace period (in seconds) to allow a token to be used after logout.
    | Useful for handling race conditions. Default: 0 (no grace period)
    |
    */
    'blacklist_grace_period' => (int) (getenv('JWT_BLACKLIST_GRACE_PERIOD') ?: 0),
];
