<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Models\WebhookEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WebhookEventController extends Controller
{
    //
    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.webhook-event';
    }

    public function index()
    {
        $tenants = WebhookEvent::query()
            ->whereNotNull('tenant_code')
            ->distinct()
            ->orderBy('tenant_code')
            ->pluck('tenant_code');

        $merchants = WebhookEvent::query()
            ->whereNotNull('merchant_name')
            ->distinct()
            ->orderBy('merchant_name')
            ->pluck('merchant_name');

        $eventTypes = WebhookEvent::query()
            ->whereNotNull('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        return view($this->folder . '.index', compact('tenants', 'merchants', 'eventTypes'));
    }

    public function webhookEventsDatatable(Request $request)
    {
        $webhookEvent = WebhookEvent::query()
                        ->select('webhook_events.*')
                        ->leftJoin('webhook_deliveries', 'webhook_deliveries.webhook_event_id', '=', 'webhook_events.id')
                        ->with(['delivery'])
                        ->when($request->tenant, function ($query) use ($request) {
                            return $query->where('tenant_code', $request->tenant);
                        })
                        ->when($request->merchant, function ($query) use ($request) {
                            return $query->where('merchant_name', $request->merchant);
                        })
                        ->when($request->event_type, function ($query) use ($request) {
                            return $query->where('event_type', $request->event_type);
                        })
                        ->when($request->start_date, function ($query) use ($request) {
                            return $query->where('occurred_at', '>=', $request->start_date);
                        })
                        ->when($request->end_date, function ($query) use ($request) {
                            return $query->where('occurred_at', '<=', $request->end_date);
                        });

        return DataTables::of($webhookEvent)
            ->addIndexColumn()
            ->editColumn('occurred_at', function ($webhookEvent) {
                return $webhookEvent->occurred_at ? $webhookEvent->occurred_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('payload', function ($webhookEvent) {
                $payloadJson = e(json_encode($webhookEvent->payload));

                return sprintf(
                    '<a style="font-size: 14px; text-align: start;cursor: pointer; text-decoration: underline; color: #007bff;" class="btn webhook-event-payload-btn" data-webhook_payload="%s" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventPayloadModal">View payload</a>',
                    $payloadJson,
                    $webhookEvent->id
                );
            })
            ->editColumn('created_at', function ($webhookEvent) {
                return $webhookEvent->created_at ? $webhookEvent->created_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('status', function ($webhookEvent) {

                switch ($webhookEvent->delivery->status) {
                    case WebhookDelivery::STATUS_PENDING:
                        return '<span class="badge badge-sm badge-secondary">Pending</span>';
                    case WebhookDelivery::STATUS_QUEUED:
                        return '<span class="badge badge-sm badge-secondary">Queued</span>';
                    case WebhookDelivery::STATUS_PROCESSING:
                        return '<span class="badge badge-sm badge-success">Processing</span>';
                    case WebhookDelivery::STATUS_RETRY_SCHEDULED:
                        return '<span class="badge badge-sm badge-warning">Retry Scheduled</span>';
                    case WebhookDelivery::STATUS_DELIVERED:
                        return '<span class="badge badge-sm badge-success">Delivered</span>';
                    case WebhookDelivery::STATUS_FAILED_PERMANENTLY:
                        return '<span class="badge badge-sm badge-danger text-wrap text-start">Failed Permanently</span>';
                    case WebhookDelivery::STATUS_CANCELLED:
                        return '<span class="badge badge-sm badge-danger">Cancelled</span>';
                    default:
                        return '<span class="badge badge-sm badge-info">N/As</span>';
                }
            })
            ->editColumn('last_http_status', function ($webhookEvent) {

                $lastHttpStatus = $webhookEvent->delivery->last_http_status ? $webhookEvent->delivery->last_http_status : '-';

                return '<span class="font-monospace text-start">' . $lastHttpStatus . '</span>';
            })
            ->editColumn('next_attempt_at', function ($webhookEvent) {

                $nextAttemptAt = $webhookEvent->delivery->next_attempt_at ? $webhookEvent->delivery->next_attempt_at : '-';

                return $nextAttemptAt ? $nextAttemptAt : '-';
            })
            ->addColumn('action', function ($webhookEvent) {
                return sprintf(
                '<a style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary webhook-event-view-btn" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventViewModal"><i class="fa fa-eye" aria-hidden="true"></i></a>',
                    $webhookEvent->id
                );

            })
            ->rawColumns(['action', 'payload', 'status', 'last_http_status'])
            ->orderColumn('status', 'webhook_deliveries.status $1')
            ->orderColumn('last_http_status', 'webhook_deliveries.last_http_status $1')
            ->orderColumn('next_attempt_at', 'webhook_deliveries.next_attempt_at $1')
            ->make(true);
    }

    public function show(string $id)
    {
        $webhookEvent = WebhookEvent::findOrFail($id);
        $webhookEvent?->load('delivery');
        return response()->json([
            'webhookEvent' => $webhookEvent
        ]);
    }
}
