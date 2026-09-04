<?php

namespace App\Domain\Webhooks\Jobs;

use App\Domain\Webhooks\Models\WebhookAlert;
use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Models\WebhookDeliveryAttempt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a webhook alert to configured recipients (spec §21.6).
 *
 * Currently supports email alerts. Future: Telegram, Slack, etc.
 * Records the alert to webhook_alerts table for audit trail.
 */
class SendWebhookAlertJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly string $deliveryId,
        public readonly array  $alertConfig,
        public readonly string $alertType,
        public readonly ?string $dedupKey = null,
    ) {
    }

    public function handle(): void
    {
        $delivery = WebhookDelivery::with(['event', 'snapshot'])->find($this->deliveryId);

        if ($delivery === null || $delivery->event === null) {
            Log::warning('SendWebhookAlertJob: delivery or event not found.', [
                'delivery_id' => $this->deliveryId,
            ]);
            return;
        }

        $latestAttempt = WebhookDeliveryAttempt::where('delivery_id', $this->deliveryId)
            ->orderBy('attempted_at', 'desc')
            ->first();

        // Send email alerts
        if (!empty($this->alertConfig['emails'])) {
            $this->sendEmailAlert($delivery, $latestAttempt);
        }

        // Record the alert to the database
        $this->recordAlert($delivery);

        // TODO: Implement Telegram, Slack, etc.
    }

    /**
     * Send an email alert to configured recipients.
     */
    protected function sendEmailAlert(WebhookDelivery $delivery, ?WebhookDeliveryAttempt $latestAttempt): void
    {
        $event = $delivery->event;
        $snapshot = $delivery->snapshot;

        $subject = $this->buildEmailSubject($delivery);
        $body = $this->buildEmailBody($delivery, $latestAttempt);

        foreach ($this->alertConfig['emails'] as $email) {
            try {
                Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)
                        ->subject($subject);
                });

                Log::info('Webhook alert email sent.', [
                    'delivery_id' => $delivery->id,
                    'alert_type' => $this->alertType,
                    'recipient' => $email,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send webhook alert email.', [
                    'delivery_id' => $delivery->id,
                    'alert_type' => $this->alertType,
                    'recipient' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Build the email subject line.
     */
    protected function buildEmailSubject(WebhookDelivery $delivery): string
    {
        $alertLabel = match ($this->alertType) {
            'delivery_warning' => 'Webhook Delivery Warning',
            'delivery_permanent_failure' => 'Webhook Delivery Failed Permanently',
            'endpoint_degraded' => 'Webhook Endpoint Degraded',
            'endpoint_down' => 'Webhook Endpoint Down',
            'endpoint_recovered' => 'Webhook Endpoint Recovered',
            default => 'Webhook Alert',
        };

        return sprintf('[SGDataPOS] %s - %s', $alertLabel, $delivery->event->event_type ?? 'Unknown Event');
    }

    /**
     * Build the email body with delivery details.
     */
    protected function buildEmailBody(WebhookDelivery $delivery, ?WebhookDeliveryAttempt $latestAttempt): string
    {
        $event = $delivery->event;
        $snapshot = $delivery->snapshot;

        $lines = [
            'SGDataPOS Webhook Alert',
            '',
            'Alert Type: ' . $this->alertType,
            'Event ID: ' . ($event->id ?? 'N/A'),
            'Event Type: ' . ($event->event_type ?? 'N/A'),
            'Delivery ID: ' . $delivery->id,
            'Merchant: ' . ($event->merchant_name ?? 'N/A') . ' (' . ($event->merchant_id ?? 'N/A') . ')',
            'Endpoint: ' . ($snapshot->name ?? 'N/A') . ' (' . ($snapshot->url ?? 'N/A') . ')',
            'Attempt Count: ' . $delivery->attempt_count,
            'Status: ' . $delivery->status,
        ];

        if ($latestAttempt) {
            $lines[] = '';
            $lines[] = 'Latest Attempt Details:';
            $lines[] = '  HTTP Status: ' . ($latestAttempt->response_status ?? 'N/A');
            $lines[] = '  Error Code: ' . ($latestAttempt->error_type ?? 'N/A');
            $lines[] = '  Error Message: ' . ($latestAttempt->error_message ?? 'N/A');
            $lines[] = '  Duration: ' . ($latestAttempt->duration_ms ?? 0) . 'ms';
            $lines[] = '  Attempted At: ' . ($latestAttempt->attempted_at?->toDateTimeString() ?? 'N/A');
        }

        $lines[] = '';
        $lines[] = '---';
        $lines[] = 'This is an automated message from SGDataPOS Central Webhook Delivery System.';

        return implode("\n", $lines);
    }

    /**
     * Record the alert to webhook_alerts table for audit trail.
     */
    protected function recordAlert(WebhookDelivery $delivery): void
    {
        WebhookAlert::create([
            'tenant_id' => $delivery->tenant_id,
            'delivery_id' => $delivery->id,
            'endpoint_health_id' => null, // TODO: link to endpoint health when implemented
            'alert_type' => $this->alertType,
            'channel' => 'email',
            'recipient' => implode(',', $this->alertConfig['emails'] ?? []),
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
            'deduplication_key' => $this->dedupKey,
        ]);
    }
}
