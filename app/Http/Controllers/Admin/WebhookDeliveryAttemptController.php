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
                    '<a style="font-size: 14px; text-align: start;cursor: pointer; text-decoration: underline; color: #007bff;" class="btn webhook-event-payload-btn" data-webhook_payload="%s" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventPayloadModal">View Content</a>',
                    $responseBodyJson,
                    $deliveryAttempt->id
                );
            })
            ->editColumn('duration_ms', function ($deliveryAttempt) {
                return $deliveryAttempt->duration_ms ? ($deliveryAttempt->duration_ms) : '-';
            })
            ->editColumn('error_type', function ($deliveryAttempt) {
                return $deliveryAttempt->error_type ? ($deliveryAttempt->error_type) : '-';
            })
            ->editColumn('error_message', function ($deliveryAttempt) {
                return $deliveryAttempt->error_message ? ($deliveryAttempt->error_message) : '-';
            })
            ->editColumn('attempted_at', function ($deliveryAttempt) {

                $attemptedAt = $deliveryAttempt->attempted_at ? $deliveryAttempt->attempted_at->format('Y-m-d H:i:s') : '-';

                return $attemptedAt;
            })
            ->rawColumns(['response_body_excerpt'])
            ->make(true);
    }

}
