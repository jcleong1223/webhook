<?php

namespace App\Domain\Webhooks\Contracts;

/**
 * Signs an outgoing webhook payload (spec §13).
 *
 * Implementations must be stateless and deterministic for a given
 * payload + secret pair. The returned timestamp and signature are
 * placed directly into the outgoing X-SGDP-* headers (spec §12).
 */
interface WebhookSigner
{
    /**
     * @param  string $payload  Raw JSON body to sign.
     * @param  string $secret   Decrypted signing secret.
     * @return array{timestamp: int, signature: string}
     *                  timestamp  — Unix epoch seconds.
     *                  signature  — Full header value, e.g. "v1=abcdef…".
     */
    public function sign(string $payload, string $secret): array;
}
