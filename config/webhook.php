<?php

return [
    'connection_timeout' => env('WEBHOOK_CONNECT_TIMEOUT', 3),
    'default_timeout' => env('WEBHOOK_DEFAULT_TIMEOUT', 10),
    'max_timeout' => env('WEBHOOK_MAX_TIMEOUT', 30),
    'default_max_attempts' => env('WEBHOOK_DEFAULT_MAX_ATTEMPTS', 8),
    'max_payload_bytes' => env('WEBHOOK_MAX_PAYLOAD_BYTES', 1048576),
    'max_response_excerpt_bytes' => env('WEBHOOK_RESPONSE_EXCERPT_BYTES', 8192),
    'allowed_clock_skew_seconds' => env('WEBHOOK_ALLOWED_CLOCK_SKEW_SECONDS', 300),
    'require_https' => env('WEBHOOK_REQUIRE_HTTPS', true),
    'allowed_ports' => array_values(array_filter(array_map('intval', explode(',', (string) env('WEBHOOK_ALLOWED_PORTS', '443'))))),
    'allow_redirects' => env('WEBHOOK_ALLOW_REDIRECTS', false),
    'warning_attempt' => env('WEBHOOK_WARNING_ATTEMPT', 3),
    'degraded_failure_count' => env('WEBHOOK_DEGRADED_FAILURE_COUNT', 10),
    'down_failure_count' => env('WEBHOOK_DOWN_FAILURE_COUNT', 20)
];
