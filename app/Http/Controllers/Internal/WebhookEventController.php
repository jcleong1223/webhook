<?php

namespace App\Http\Controllers\Internal;

use App\Domain\Webhooks\Actions\AcceptWebhookEvent;
use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Services\EndpointHealthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptWebhookEventRequest;
use App\Traits\WebhookRespFormatter;
use Illuminate\Http\JsonResponse;

class WebhookEventController extends Controller
{
    use WebhookRespFormatter;

    public function __construct(
        protected EndpointHealthService $endpointHealthService
    )
    {
        $this->endpointHealthService = $endpointHealthService;
    }

    /**
     * POST /api/internal/v1/webhook-events
     *
     * Accepts an event from the legacy backoffice (spec §7.3):
     * 202 Accepted for new events, 200 OK with duplicate=true when the
     * source event was already ingested (spec §9.4–§9.5).
     */
    public function store(AcceptWebhookEventRequest $request, AcceptWebhookEvent $acceptWebhookEvent): JsonResponse
    {
        $result = $acceptWebhookEvent->handle($request->validated());

        $event = $result['event'];
        $delivery = $result['delivery'];

        if ($result['duplicate']) {
            return $this->duplicateResponse(
                $event->id,
                $delivery?->id ?? '',
                $delivery?->status ?? 'queued'
            );
        }

        return $this->acceptedResponse($event->id, $delivery->id, $delivery->status);
    }

    public function testing()
    {
        $deliver = WebhookDelivery::find('dlv_01M1N8E91WAHHD07AJD2D04E5C');
        $this->endpointHealthService->recordEndpointHealthStatus($deliver);
    }
}
