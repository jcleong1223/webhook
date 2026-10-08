<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookEndpointSnapshot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
