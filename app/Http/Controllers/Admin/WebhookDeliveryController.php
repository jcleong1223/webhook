<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookDeliveryController extends Controller
{
    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.webhook-delivery';
    }

    public function index()
    {
        $tenants = WebhookDelivery::query()
            ->whereNotNull('tenant_id')
            ->distinct()
            ->orderBy('tenant_id')
            ->pluck('tenant_id');

        $statuses = WebhookDelivery::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $datesColumns = WebhookDelivery::$dateFields;

        return view($this->folder . '.index', compact('tenants', 'statuses', 'datesColumns'));
    }
}
