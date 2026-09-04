<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Webhooks\Contracts\WebhookSigner;

/**
 * HMAC-SHA256 signer for outgoing merchant webhooks (spec §13.1).
 *
 * signed_payload = timestamp + "." + raw_json_body
 * signature      = HMAC-SHA256(signing_secret, signed_payload)
 * header value   = "v1=" + hex(signature)
 */
class HmacWebhookSigner implements WebhookSigner
{
    /**
     * @return array{timestamp: int, signature: string}
     */
    public function sign(string $payload, string $secret): array
    {
        $timestamp = time();
        $signedInput = $timestamp . '.' . $payload;
        $signature = 'v1=' . hash_hmac('sha256', $signedInput, $secret);

        return [
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
    }
}
