<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Webhooks\Enums\EndpointHealthStatus;
use App\Domain\Webhooks\Enums\WebhookAlertType;
use App\Domain\Webhooks\Jobs\SendWebhookAlertJob;
use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Models\WebhookEndpointHealth;
use App\Domain\Webhooks\Policies\WebhookAlertPolicy;

/**
 * Tracks endpoint-wide health after each delivery attempt (spec §19.5).
 *
 * Health transitions (spec §21.3–§21.5):
 *   healthy  →  degraded    when consecutive failures reach degraded_failure_count (default 10)
 *   degraded →  down        when consecutive failures reach down_failure_count (default 20)
 *   degraded / down → healthy  on next successful delivery (recovery)
 *
 * Counters (consecutive_failures, recent_success_count, recent_failure_count)
 * are incremented atomically to survive concurrent delivery workers.
 *
 * TODO (Phase 5): dispatch deduplicated alerts on status transitions
 *                 (endpoint_degraded, endpoint_down, endpoint_recovered).
 */
class EndpointHealthService
{
    public function __construct(
        protected WebhookAlertPolicy $alertPolicy,
    ) {
    }

    /**
     * Evaluate and persist endpoint health after a delivery attempt.
     *
     * Reads $delivery->status (already set by DeliverWebhook) to determine
     * whether this attempt was a success or failure, then delegates.
     */
    public function recordEndpointHealthStatus(WebhookDelivery $delivery): void
    {
        $snapshot = $delivery->snapshot;

        if ($snapshot === null) {
            return;
        }

        $health = WebhookEndpointHealth::firstOrCreate(
            [
                'tenant_id'           => $snapshot->tenant_id,
                'legacy_webhook_id'   => $snapshot->legacy_webhook_id,
            ],
            [
                'endpoint_url_hash'    => hash('sha256', $snapshot->url),
                'status'               => EndpointHealthStatus::HEALTHY->value,
                'consecutive_failures' => 0,
                'recent_success_count' => 0,
                'recent_failure_count' => 0,
            ],
        );

        match ($delivery->status) {
            WebhookDelivery::STATUS_DELIVERED          => $this->recordSuccess($health, $delivery),
            WebhookDelivery::STATUS_RETRY_SCHEDULED,
            WebhookDelivery::STATUS_FAILED_PERMANENTLY => $this->recordFailure($health, $delivery),
            default                                    => null, // cancelled, processing — no-op
        };
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Success path (§21.5)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Record a successful delivery:
     *  - Reset consecutive_failures and recent_failure_count to 0.
     *  - If endpoint was degraded/down → mark recovered (set recovered_at).
     *  - Otherwise → ensure status is healthy.
     */
    protected function recordSuccess(WebhookEndpointHealth $health, $delivery): void
    {
        // Atomic counter resets
        $health->newQuery()
            ->where('id', $health->id)
            ->update([
                'consecutive_failures' => 0,
                'recent_failure_count' => 0,
            ]);

        $wasUnhealthy = in_array($health->status, [
            EndpointHealthStatus::DEGRADED->value,
            EndpointHealthStatus::DOWN->value,
        ], true);

        $updateData = [
            'status'               => EndpointHealthStatus::HEALTHY->value,
            'recent_success_count' => $health->recent_success_count + 1,
            'last_success_at'      => now(),
        ];

        if ($wasUnhealthy) {
            $updateData['recovered_at'] = now();

            /****** dispatch endpoint_recovered alert (§21.5). ******/
            $alertDecision = $this->alertPolicy->shouldAlert($delivery, WebhookAlertType::ENDPOINT_RECOVERED->value);

            if ($alertDecision['should_send']) {
                SendWebhookAlertJob::dispatch(
                    $delivery->id,
                    $alertDecision['recipients'],
                    WebhookAlertType::ENDPOINT_RECOVERED->value,
                    $alertDecision['dedup_key'],
                )->onQueue('webhook-alerts');
            }

        }

        $health->update($updateData);
        $health->refresh();
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Failure path (§21.3 / §21.4)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Record a failed delivery:
     *  - Increment consecutive_failures and recent_failure_count atomically.
     *  - Reset recent_success_count to 0.
     *  - Classify new status: healthy → degraded (≥10) → down (≥20).
     *  - Set degraded_at / update last_failure_at.
     */
    protected function recordFailure(WebhookEndpointHealth $health, $delivery): void
    {
        // Atomic counter increments (safe under concurrent workers)
        $health->newQuery()
            ->where('id', $health->id)
            ->increment('consecutive_failures', 1, [
                'recent_failure_count' => $health->recent_failure_count + 1,
                'recent_success_count' => 0,
            ]);

        $health->refresh();

        $consecutiveFailures = $health->consecutive_failures;
        $degradedThreshold   = config('webhook.degraded_failure_count', 10);
        $downThreshold       = config('webhook.down_failure_count', 20);

        $updateData = [
            'last_failure_at' => now(),
        ];

        if ($consecutiveFailures >= $downThreshold) {
            $updateData['status'] = EndpointHealthStatus::DOWN->value;
            /****** Dispatch endpoint_down alert (§21.4). ******/
            $alertDecision = $this->alertPolicy->shouldAlert($delivery, WebhookAlertType::ENDPOINT_DOWN->value);

            if ($alertDecision['should_send']) {
                SendWebhookAlertJob::dispatch(
                    $delivery->id,
                    $alertDecision['recipients'],
                    WebhookAlertType::ENDPOINT_DOWN->value,
                    $alertDecision['dedup_key'],
                )->onQueue('webhook-alerts');
            }

        } elseif ($consecutiveFailures >= $degradedThreshold) {
            $updateData['status'] = EndpointHealthStatus::DEGRADED->value;
            // Set degraded_at only on the transition into degraded
            if ($health->status !== EndpointHealthStatus::DEGRADED->value
                && $health->status !== EndpointHealthStatus::DOWN->value) {
                $updateData['degraded_at'] = now();
            }
            /****** dispatch endpoint_degraded alert (§21.3). ******/
            $alertDecision = $this->alertPolicy->shouldAlert($delivery, WebhookAlertType::ENDPOINT_DEGRADED->value);

            if ($alertDecision['should_send']) {
                SendWebhookAlertJob::dispatch(
                    $delivery->id,
                    $alertDecision['recipients'],
                    WebhookAlertType::ENDPOINT_DEGRADED->value,
                    $alertDecision['dedup_key'],
                )->onQueue('webhook-alerts');
            }
        } else {

            /****** dispatch endpoint_degraded alert (§21.1). ******/
            $alertDecision = $this->alertPolicy->shouldAlert($delivery, WebhookAlertType::DELIVERY_WARNING->value);

            if ($alertDecision['should_send']) {
                SendWebhookAlertJob::dispatch(
                    $delivery->id,
                    $alertDecision['recipients'],
                    WebhookAlertType::DELIVERY_WARNING->value,
                    $alertDecision['dedup_key'],
                )->onQueue('webhook-alerts');
            }
        }

        $health->update($updateData);
    }
}
