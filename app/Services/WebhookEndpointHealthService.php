<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookEndpointHealthService
{
    public function __construct(

    ) {}

    public function endpointHealthWithSnapshot()
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