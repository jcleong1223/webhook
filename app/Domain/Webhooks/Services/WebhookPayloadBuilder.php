<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Webhooks\Models\WebhookEvent;

/**
 * Builds the standard §11.1 outgoing webhook envelope from a WebhookEvent.
 *
 * The envelope wraps the immutable business payload (stored at ingestion)
 * with tenant/merchant metadata and event identifiers. The returned array
 * is JSON-encoded by the caller and signed before transmission.
 *
 * Payload rules (spec §11.2):
 *  - IDs are strings.
 *  - Monetary values are decimal strings.
 *  - Timestamps use ISO 8601.
 *  - Dates without time use YYYY-MM-DD.
 *  - Internal database columns are never exposed.
 */
class WebhookPayloadBuilder
{
    /**
     * Build the §11.1 envelope for the given event.
     *
     * @return array  JSON-serializable envelope.
     */
    public function build(WebhookEvent $event): array
    {
        return [
            'id'               => $event->id,
            'type'             => $event->event_type,
            'api_version'      => $event->api_version ?? 'v1',
            'created_at'       => $event->occurred_at->toIso8601String(),
            'resource_version' => $event->resource_version ?? 1,
            'tenant'           => [
                'id'   => $event->tenant_id,
                'code' => $event->tenant_code,
            ],
            'merchant'         => [
                'id'   => $event->merchant_id,
                'name' => $event->merchant_name ?? '',
            ],
            'data'             => [
                'object' => $event->payload,
            ],
        ];
    }
}
