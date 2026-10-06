<?php

namespace App\Http\Controllers\Admin;

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
            ->addColumn('action', function ($webhookEvent) {
                return sprintf(
                '<a style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary webhook-event-view-btn" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventViewModal"><i class="fa fa-eye" aria-hidden="true"></i></a>',
                    $webhookEvent->id
                );

            })
            ->rawColumns(['action', 'payload'])
            ->make(true);
    }

    public function show(int $id)
    {
        $webhookEvent = WebhookEvent::findOrFail($id);

        return response()->json([
            'webhookEvent' => $webhookEvent
        ]);
    }
}
