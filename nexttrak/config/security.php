<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers for your application. These headers help
    | protect against various attacks and improve security posture.
    |
    */

    'headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy headers to prevent XSS attacks
    | and other injection-based vulnerabilities.
    |
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', false),
        'directives' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
            'style-src' => ["'self'", "'unsafe-inline'"],
            'img-src' => ["'self'", 'data:', 'https:'],
            'font-src' => ["'self'"],
            'connect-src' => ["'self'"],
            'frame-src' => ["'none'"],
            'object-src' => ["'none'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various routes and actions.
    |
    */

    'rate_limiting' => [
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Configure session security settings.
    |
    */

    'session' => [
        'secure' => env('SESSION_SECURE_COOKIE', false),
        'http_only' => true,
        'same_site' => 'lax',
    ],

]; 