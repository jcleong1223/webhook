<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Enums\EndpointHealthStatus;
use App\Domain\Webhooks\Models\WebhookEndpointHealth;
use App\Http\Controllers\Controller;
use App\Services\WebhookEndpointHealthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WebhookEndpointHealthController extends Controller
{
    //
    protected $folder = '';

    public function __construct(protected WebhookEndpointHealthService $endpointHealthService)
    {
        $this->folder = 'admin.endpoint-health';
    }

    public function index()
    {
        $tenants = WebhookEndpointHealth::query()
            ->whereNotNull('tenant_id')
            ->distinct()
            ->orderBy('tenant_id')
            ->pluck('tenant_id');

        $statuses = WebhookEndpointHealth::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $datesColumns = WebhookEndpointHealth::$dateColumns;

        return view($this->folder . '.index', compact('tenants', 'statuses', 'datesColumns'));
    }

    public function endpointHealthDatatable(Request $request)
    {
        $endpointHealth = $this->endpointHealthService->endpointHealthWithSnapshot();

        return DataTables::of($endpointHealth)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                switch ($row->health_status) {
                    case EndpointHealthStatus::HEALTHY->value:
                        return '<span class="badge badge-sm badge-success">Healthy</span>';
                    case EndpointHealthStatus::DEGRADED->value:
                        return '<span class="badge badge-sm badge-warning">Degraded</span>';
                    case EndpointHealthStatus::DOWN->value:
                        return '<span class="badge badge-sm badge-danger">Down</span>';
                    case EndpointHealthStatus::DISABLED->value:
                        return '<span class="badge badge-sm badge-secondary">Disabled</span>';
                    default:
                        return '<span class="badge badge-sm badge-info">N/A</span>';
                }
            })
            ->editColumn('success_rate', fn($row) => number_format(($row->recent_success_count / ($row->snapshot_count ?? 1)) * 100, 2). '%' ?: '-')
            ->editColumn('last_success_at', fn($row) => $row->last_success_at ?: '-')
            ->editColumn('last_failure_at', fn($row) => $row->last_failure_at ?: '-')
            ->editColumn('degraded_at', fn($row) => $row->degraded_at ?: '-')
            ->editColumn('recovered_at', fn($row) => $row->recovered_at ?: '-')
            ->editColumn('last_alert_at', fn($row) => $row->last_alert_at ?: '-')
            ->editColumn('health_created_at', fn($row) => $row->health_created_at ?: '-')
            ->addColumn('action', function ($row) {
                return sprintf(
                    '<a href="%s" style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>',
                    route('admin.endpoint-health.show', $row->health_id)
                );
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function show(int $healthId)
    {
        $endpointHealths = $this->endpointHealthService->endpointHealthWithSnapshot()->get();
        $endpointHealth = $endpointHealths->where('health_id', $healthId)->first();

        return view($this->folder . '.show', compact('endpointHealth'));
    }
}
