<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookDeliveryAttempt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WebhookDeliveryAttemptController extends Controller
{
    //
    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.delivery-attempt';
    }

    public function index()
    {
        $tenants = WebhookDeliveryAttempt::query()
            ->whereNotNull('tenant_id')
            ->distinct()
            ->orderBy('tenant_id')
            ->pluck('tenant_id');

        $responseStatuses = WebhookDeliveryAttempt::query()
                            ->whereNotNull('response_status')
                            ->distinct()
                            ->orderBy('response_status')
                            ->pluck('response_status');

        return view($this->folder . '.index', compact('tenants', 'responseStatuses'));
    }

    public function deliveryAttemptDatatable(Request $attempt)
    {

        $deliveryAttempt = WebhookDeliveryAttempt::query();

        return DataTables::of($deliveryAttempt)
            ->addIndexColumn()
            ->editColumn('response_body_excerpt', function ($deliveryAttempt) {
                $responseBodyJson = e(json_encode($deliveryAttempt->response_body_excerpt));

                return sprintf(
                    '<a style="font-size: 14px; text-align: start;cursor: pointer; text-decoration: underline; color: #007bff;" class="btn webhook-event-payload-btn" data-webhook_payload="%s" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventPayloadModal">View payload</a>',
                    $responseBodyJson,
                    $deliveryAttempt->id
                );
            })
            ->editColumn('last_http_status', function ($webhookEvent) {

                $lastHttpStatus = $webhookEvent->delivery->last_http_status ? $webhookEvent->delivery->last_http_status : '-';

                return '<span class="font-monospace text-start">' . $lastHttpStatus . '</span>';
            })
            ->editColumn('attempted_at', function ($deliveryAttempt) {

                $attemptedAt = $deliveryAttempt->attempted_at ? $deliveryAttempt->attempted_at->format('Y-m-d H:i:s') : '-';

                return $attemptedAt;
            })
            ->rawColumns(['action', 'payload', 'status', 'last_http_status'])
            ->orderColumn('status', 'webhook_deliveries.status $1')
            ->orderColumn('last_http_status', 'webhook_deliveries.last_http_status $1')
            ->orderColumn('next_attempt_at', 'webhook_deliveries.next_attempt_at $1')
            ->make(true);
    }

}
