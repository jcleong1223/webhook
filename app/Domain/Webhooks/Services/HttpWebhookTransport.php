<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Webhooks\Contracts\WebhookTransport;
use App\Domain\Webhooks\Enums\WebhookErrorCode;
use Illuminate\Support\Facades\Http;

/**
 * HTTP transport for outbound merchant webhook delivery (spec §18).
 *
 * Responsibilities:
 *  - Send HTTP POST with separate connect + total timeouts.
 *  - Disable redirects (spec §18).
 *  - Truncate response body to configured max excerpt bytes (spec §18).
 *  - Sanitize response headers (spec §19.4).
 *  - Translate network-level exceptions into WebhookErrorCode values (spec §29).
 *  - NEVER throw — all outcomes are captured in the returned array.
 */
class HttpWebhookTransport implements WebhookTransport
{
    public function send(string $url, string $body, array $headers, int $connectTimeout, int $timeout): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::connectTimeout($connectTimeout)
                ->timeout($timeout)
                ->withHeaders($headers)
                ->withBody($body, 'application/json')
                ->post($url);

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $maxExcerpt = config('webhook.max_response_excerpt_bytes', 8192);

            $responseBody = $response->body();
            if (strlen($responseBody) > $maxExcerpt) {
                $responseBody = substr($responseBody, 0, $maxExcerpt);
            }

            return [
                'http_status'      => $response->status(),
                'response_headers' => $this->sanitizeResponseHeaders($response->headers()),
                'response_body'    => $responseBody,
                'duration_ms'      => $durationMs,
                'error_code'       => null,
                'error_message'    => null,
            ];
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            return [
                'http_status'      => null,
                'response_headers' => [],
                'response_body'    => null,
                'duration_ms'      => $durationMs,
                'error_code'       => $this->classifyTransportError($e),
                'error_message'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Map cURL / network exception to a WebhookErrorCode value (spec §29).
     */
    protected function classifyTransportError(\Throwable $e): string
    {
        $message = $e->getMessage();

        return match (true) {
            str_contains($message, 'cURL error 6')  || str_contains($message, 'Could not resolve')
                => WebhookErrorCode::DNS_RESOLUTION_FAILED->value,
            str_contains($message, 'cURL error 7')  || str_contains($message, 'Connection refused')
                => WebhookErrorCode::CONNECTION_REFUSED->value,
            str_contains($message, 'cURL error 28') || str_contains($message, 'timed out')
                => WebhookErrorCode::CONNECTION_TIMEOUT->value,
            str_contains($message, 'cURL error 35') || str_contains($message, 'SSL') || str_contains($message, 'TLS')
                => WebhookErrorCode::TLS_HANDSHAKE_FAILED->value,
            str_contains($message, 'cURL error 51') || str_contains($message, 'certificate')
                => WebhookErrorCode::INVALID_CERTIFICATE->value,
            str_contains($message, 'cURL error 47') || str_contains($message, 'redirect')
                => WebhookErrorCode::INVALID_REDIRECT->value,
            default
                => WebhookErrorCode::UNEXPECTED_DELIVERY_EXCEPTION->value,
        };
    }

    /**
     * Redact sensitive response headers (spec §19.4 / §25.5).
     */
    protected function sanitizeResponseHeaders($headers): array
    {
        $sensitive = ['authorization', 'cookie', 'set-cookie', 'proxy-authorization'];
        $sanitized = [];

        foreach ($headers as $key => $values) {
            if (in_array(strtolower($key), $sensitive, true)) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                $sanitized[$key] = is_array($values) ? implode(', ', $values) : $values;
            }
        }

        return $sanitized;
    }
}
