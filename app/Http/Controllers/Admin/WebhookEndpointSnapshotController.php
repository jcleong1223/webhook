<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookEndpointSnapshot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WebhookEndpointSnapshotController extends Controller
{
    //
    public function __construct()
    {

    }

    public function show(int $id)
    {
        $webhookEndpointSnapshot = WebhookEndpointSnapshot::findOrFail($id);
        return response()->json([
            'webhookEndpointSnapshot' => $webhookEndpointSnapshot
        ]);
    }

    public function endpointSnapshotDatatable(Request $request)
    {
        $webhookEndpointSnapshot = WebhookEndpointSnapshot::query()
                                    ->when($request->tenant, function ($query) use ($request) {
                                        return $query->where('tenant_id', $request->tenant);
                                    })
                                    ->when($request->legacy_webhook_id, function ($query) use ($request) {
                                        return $query->where('legacy_webhook_id', $request->legacy_webhook_id);
                                    });

        return DataTables::of($webhookEndpointSnapshot)
            ->addIndexColumn()
            ->editColumn('alert_configuration', function ($webhookEndpointSnapshot) {
                $configJson = e(json_encode($webhookEndpointSnapshot->alert_configuration));

                return sprintf(
                '<a style="font-size: 14px; text-align: start;cursor: pointer; text-decoration: underline; color: #007bff;" class="btn snapshot-config-btn" data-snapshot-config="%s" data-snpst-id="%s" data-bs-toggle="modal" data-bs-target="#alertConfigShowModal">View config</a>',
                    $configJson,
                    $webhookEndpointSnapshot->id
                );
            })
            ->editColumn('created_at', function ($webhookEndpointSnapshot) {
                return $webhookEndpointSnapshot->created_at ? $webhookEndpointSnapshot->created_at->format('Y-m-d H:i:s') : '-';
            })
            ->rawColumns(['alert_configuration'])
            ->make(true);


    }
}
