<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookEndpointHealthService
{
    public function __construct(

    ) {}

    public function endpointHealthWithSnapshot(array $filters = [])
    {
        $latestSnapshots = DB::table('webhook_endpoint_snapshots')
            ->select(
                'tenant_id',
                'legacy_webhook_id',
                DB::raw('MAX(id) as max_snapshot_id'),
                DB::raw('COUNT(id) as snapshot_count')
            )
            ->groupBy('tenant_id', 'legacy_webhook_id');

        $endpointHealth = DB::table('webhook_endpoint_healths as health')
            ->leftJoinSub($latestSnapshots, 'latest_snap', function ($join) {
                $join->on('health.tenant_id', '=', 'latest_snap.tenant_id')
                    ->on('health.legacy_webhook_id', '=', 'latest_snap.legacy_webhook_id');
            })
            ->leftJoin('webhook_endpoint_snapshots as snapshot', 'latest_snap.max_snapshot_id', '=', 'snapshot.id')
            ->when($filters['tenant'] ?? null, function ($query, $tenantId) {
                return $query->where('health.tenant_id', $tenantId);
            })
            ->when($filters['health_status'], function ($query) use ($filters) {
                return $query->where('health.status', $filters['health_status']);
            })
            ->when($filters['date_type'], function ($query) use ($filters) {

                $dateColumn = match ($filters['date_type']) {
                    'last_success_at'   => 'health.last_success_at',
                    'last_failure_at'   => 'health.last_failure_at',
                    'degraded_at'       => 'health.degraded_at',
                    'recovered_at'      => 'health.recovered_at',
                    'last_alert_at'     => 'health.last_alert_at',
                    default             => 'health.created_at',
                };

                return $query
                    ->when($filters['start_date'], function ($query) use ($filters, $dateColumn) {
                        return $query->where(
                            $dateColumn,
                            '>=',
                            Carbon::parse($filters['start_date'])->startOfDay()
                        );
                    })
                    ->when($filters['end_date'], function ($query) use ($filters, $dateColumn) {
                        return $query->where(
                            $dateColumn,
                            '<=',
                            Carbon::parse($filters['end_date'])->endOfDay()
                        );
                    });
            })
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
                'snapshot.name as snapshot_name',
                'snapshot.url as snapshot_url',
                'snapshot.created_at as snapshot_created_at',

                DB::raw('COALESCE(latest_snap.snapshot_count, 0) as snapshot_count'),
            ]);

        return $endpointHealth;
    }



}