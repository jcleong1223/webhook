<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookEndpointHealth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WebhookEndpointHealthController extends Controller
{
    //
    protected $folder = '';

    public function __construct()
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

        $latestSnapshots = DB::table('webhook_endpoint_snapshots')
            ->select(
                'tenant_id',
                'legacy_webhook_id',
                DB::raw('MAX(id) as max_snapshot_id'),
                DB::raw('COUNT(id) as snapshot_count') // Aggregated count directly in the group
            )
            ->groupBy('tenant_id', 'legacy_webhook_id');

        $endpointHealth = DB::table('webhook_endpoint_healths as health')
            ->leftJoinSub($latestSnapshots, 'latest_snap', function ($join) {
                $join->on('health.tenant_id', '=', 'latest_snap.tenant_id')
                    ->on('health.legacy_webhook_id', '=', 'latest_snap.legacy_webhook_id');
            })
            ->leftJoin('webhook_endpoint_snapshots as snapshot', 'latest_snap.max_snapshot_id', '=', 'snapshot.id')
            ->select([
                'health.id as health_id',
                'health.tenant_id',
                'health.legacy_webhook_id',
                'health.status as health_status',
                'health.consecutive_failures',
                'health.recent_success_count',
                'health.recent_failure_count',
                'health.last_success_at',
                'health.last_failure_at',
                'health.degraded_at',
                'health.recovered_at',
                'health.last_alert_at',
                'health.created_at as health_created_at',
                'snapshot.id as snapshot_id',
                'snapshot.created_at as snapshot_created_at',

                DB::raw('COALESCE(latest_snap.snapshot_count, 0) as snapshot_count'),
            ]);

        return DataTables::of($endpointHealth)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                switch ($row->health_status) {
                    case WebhookEndpointHealth::STATUS_HEALTHY:
                        return '<span class="badge badge-sm badge-success">Healthy</span>';
                    case WebhookEndpointHealth::STATUS_DEGRADED:
                        return '<span class="badge badge-sm badge-warning">Degraded</span>';
                    case WebhookEndpointHealth::STATUS_DOWN:
                        return '<span class="badge badge-sm badge-danger">Down</span>';
                    case WebhookEndpointHealth::STATUS_DISABLED:
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
                    route('admin.webhook-delivery.show', $row->health_id)
                );
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }
}
