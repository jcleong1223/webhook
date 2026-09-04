<?php

namespace App\Domain\Webhooks\Policies;

use App\Domain\Webhooks\Enums\WebhookAlertType;
use App\Domain\Webhooks\Models\WebhookAlert;
use App\Domain\Webhooks\Models\WebhookDelivery;

/**
 * Alert policy for webhook deliveries (spec §21).
 *
 * Determines whether an alert should be sent for a given delivery outcome,
 * enforces deduplication to prevent alert spam (spec §21.7), and extracts
 * recipient configuration from the endpoint snapshot.
 *
 * Alert types (spec §21.1–§21.5):
 *  - delivery_warning:          After N failed attempts (configurable)
 *  - delivery_permanent_failure: All attempts exhausted
 *  - endpoint_degraded:         10+ consecutive failures
 *  - endpoint_down:             20+ consecutive failures
 *  - endpoint_recovered:        Endpoint recovers from degraded/down
 */
class WebhookAlertPolicy
{
    /**
     * Determine whether an alert should be sent and return the configuration.
     *
     * @param  WebhookDelivery $delivery
     * @param  string          $alertType  One of WebhookAlertType enum values.
     * @return array{should_send: bool, recipients: array, dedup_key: string|null}
     */
    public function shouldAlert(WebhookDelivery $delivery, string $alertType): array
    {
        $snapshot = $delivery->snapshot;

        if ($snapshot === null) {
            return ['should_send' => false, 'recipients' => [], 'dedup_key' => null];
        }

        $alertConfig = $snapshot->alert_configuration ?? [];

        // Extract recipients from alert config
        $recipients = $this->extractRecipients($alertConfig);

        if (empty($recipients['emails'])) {
            return ['should_send' => false, 'recipients' => [], 'dedup_key' => null];
        }

        // Check if this alert type is enabled
        if (!$this->isAlertEnabled($alertConfig, $alertType, $delivery)) {
            return ['should_send' => false, 'recipients' => $recipients, 'dedup_key' => null];
        }

        // Generate deduplication key (spec §21.7)
        $dedupKey = $this->buildDedupKey($delivery, $alertType);

        // Check if a similar alert was sent recently (within 1 hour)
        if ($this->wasAlertSentRecently($dedupKey)) {
            return ['should_send' => false, 'recipients' => $recipients, 'dedup_key' => $dedupKey];
        }

        return ['should_send' => true, 'recipients' => $recipients, 'dedup_key' => $dedupKey];
    }

    /**
     * Check if the alert type is enabled in the configuration.
     */
    protected function isAlertEnabled(array $alertConfig, string $alertType, WebhookDelivery $delivery): bool
    {
        $threshold = $alertConfig['notify_after_attempt'] ?? config('webhook.warning_attempt', 3);

        return match ($alertType) {
            WebhookAlertType::DELIVERY_WARNING->value => $delivery->attempt_count > $threshold,
            WebhookAlertType::DELIVERY_PERMANENT_FAILURE->value => $alertConfig['notify_on_permanent_failure'] ?? true,
            WebhookAlertType::ENDPOINT_DEGRADED->value => true,
            WebhookAlertType::ENDPOINT_DOWN->value => true,
            WebhookAlertType::ENDPOINT_RECOVERED->value => $alertConfig['notify_on_recovery'] ?? true,
            default => false,
        };
    }

    /**
     * Extract recipient configuration from the alert config.
     *
     * @return array{emails: array, telegram: array}
     */
    protected function extractRecipients(array $alertConfig): array
    {
        return [
            'emails' => $alertConfig['emails'] ?? [],
            'telegram' => $alertConfig['telegram'] ?? [],
        ];
    }

    /**
     * Build a deduplication key for the alert (spec §21.7).
     *
     * Format: tenant:legacy_webhook_id:alert_type:delivery_id
     * This prevents sending one alert for every failed event on the same endpoint.
     */
    protected function buildDedupKey(WebhookDelivery $delivery, string $alertType): string
    {
        return sprintf(
            '%s:%s:%s:%s',
            $delivery->tenant_id,
            $delivery->snapshot->legacy_webhook_id,
            $$delivery->snapshot->url,
            $alertType,
            $delivery->id,
        );
    }

    /**
     * Check if an alert with this dedup key was sent within the last hour.
     */
    protected function wasAlertSentRecently(string $dedupKey): bool
    {
        return WebhookAlert::where('deduplication_key', $dedupKey)
            ->where('sent_at', '>=', now()->subHour())
            ->exists();
    }
}
