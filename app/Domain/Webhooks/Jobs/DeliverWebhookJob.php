<?php

namespace App\Domain\Webhooks\Jobs;

use App\Domain\Webhooks\Actions\DeliverWebhook;
use App\Domain\Webhooks\Models\WebhookDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Performs exactly one HTTP delivery attempt per job (spec §20.3).
 *
 * Retries are scheduled as new delayed jobs by DeliverWebhook,
 * never looped inside this job. The $tries = 1 guard ensures the
 * queue worker itself does not auto-retry on unexpected exceptions.
 */
class DeliverWebhookJob implements ShouldQueue
{
    use Queueable;

    /** One attempt per job — retry policy is driven by database state (spec §20.3). */
    public int $tries = 1;

    public function __construct(public readonly string $deliveryId)
    {
    }

    public function handle(DeliverWebhook $deliverWebhook): void
    {
        $delivery = WebhookDelivery::with(['event', 'snapshot'])->find($this->deliveryId);

        if ($delivery === null) {
            // Delivery was deleted or cancelled before the worker picked it up — exit silently.
            return;
        }

        $deliverWebhook->handle($delivery);
    }
}
