<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SMTP probe toggle
    |--------------------------------------------------------------------------
    |
    | When disabled, MX/DNS validation still runs but SMTP handshake is skipped.
    |
    */
    'smtp_probe' => env('EMAIL_CHECKER_SMTP_PROBE', true),

    /*
    |--------------------------------------------------------------------------
    | SMTP connection settings
    |--------------------------------------------------------------------------
    */
    'smtp_port' => (int) env('EMAIL_CHECKER_SMTP_PORT', 25),
    'smtp_timeout_seconds' => (int) env('EMAIL_CHECKER_SMTP_TIMEOUT_SECONDS', 5),

    /*
    |--------------------------------------------------------------------------
    | Sender address used during SMTP probe
    |--------------------------------------------------------------------------
    */
    'from_email' => env('EMAIL_CHECKER_SET_FROM', 'example@example.com'),
];
