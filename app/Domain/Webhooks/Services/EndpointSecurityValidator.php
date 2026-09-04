<?php

namespace App\Domain\Webhooks\Services;

/**
 * Validates merchant-provided endpoint URLs against SSRF risks (spec §25.1–§25.4).
 *
 * Returns null when the URL is acceptable, or a human-readable rejection reason.
 */
class EndpointSecurityValidator
{
    public function validateUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return 'The endpoint URL is malformed.';
        }

        $scheme = strtolower($parts['scheme']);

        if (!in_array($scheme, ['https', 'http'], true)) {
            return 'The endpoint URL must use HTTP or HTTPS.';
        }

        if ((bool) config('webhook.require_https', true) && $scheme !== 'https') {
            return 'The endpoint URL must use HTTPS in production.';
        }

        // URLs with embedded credentials (https://user:pass@host) are rejected (spec §25.1)
        if (isset($parts['user']) || isset($parts['pass'])) {
            return 'The endpoint URL must not contain embedded credentials.';
        }

        // Restricted ports, preferably 443 only (spec §25.4)
        $allowedPorts = config('webhook.allowed_ports', [443]);
        if (isset($parts['port']) && !in_array((int) $parts['port'], $allowedPorts, true)) {
            return 'The endpoint URL uses a port that is not allowed.';
        }

        $host = strtolower($parts['host']);

        // Strip IPv6 literal brackets
        if (str_starts_with($host, '[') && str_ends_with($host, ']')) {
            $host = substr($host, 1, -1);
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // Rejects loopback, private IPv4/IPv6 ranges, link-local (incl. 169.254.169.254
            // cloud metadata), multicast and reserved addresses for IP literals
            if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return 'The endpoint URL must not point to a private, loopback or reserved IP address.';
            }
        } else {
            if ($this->isBlockedHostname($host)) {
                return 'The endpoint URL must not point to an internal hostname.';
            }

            // TODO (Phase 8, spec §25.3): resolve DNS and validate every returned IP
            // before connecting, to defend against DNS rebinding. Combine with
            // egress firewall rules at infrastructure level.
        }

        return null;
    }

    protected function isBlockedHostname(string $host): bool
    {
        $blocked = ['localhost', 'metadata.google.internal', 'metadata'];

        if (in_array($host, $blocked, true)) {
            return true;
        }

        $blockedSuffixes = ['.local', '.localhost', '.internal', '.lan', '.intranet', '.home', '.corp'];

        foreach ($blockedSuffixes as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
