<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WebhookEventController extends Controller
{
    //

    public function __construct()
    {

    }

    public function index()
    {
        return view('admin.webhook-events.index');
    }

    public function webhookEventsDatatable()
    {
        $webhookEvent = WebhookEvent::query();

        return DataTables::of($webhookEvent)
            ->addIndexColumn()
            ->editColumn('occurred_at', function ($webhookEvent) {
                return $webhookEvent->occurred_at ? $webhookEvent->occurred_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('payload', function ($webhookEvent) {
                $payloadJson = e(json_encode($webhookEvent->payload));

                return sprintf(
                    '<a style="text-align: start;cursor: pointer; text-decoration: underline; color: #007bff;" class="btn webhook-event-payload-btn" data-webhook_payload="%s" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventPayloadModal">View payload</a>',
                    $payloadJson,
                    $webhookEvent->id
                );
            })
            ->editColumn('created_at', function ($webhookEvent) {
                return $webhookEvent->created_at ? $webhookEvent->created_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('action', function ($webhookEvent) {
                return sprintf(
                    '<a class="btn btn-primary webhook-event-view-btn" data-id="%s" data-bs-toggle="modal" data-bs-target="#webhookEventViewModal"><i class="fa fa-eye" aria-hidden="true"></i></a>',
                    $webhookEvent->id
                );

            })
            ->rawColumns(['action', 'payload'])
            ->make(true);
    }

    public function show($id)
    {
        $webhookEvent = WebhookEvent::findOrFail($id);



        return response()->json([
            'webhookEvent' => $webhookEvent
        ]);
    }
}
