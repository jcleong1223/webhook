<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpointHealth extends Model
{
    protected $fillable = [
        'tenant_id',
        'legacy_webhook_id',
        'endpoint_url_hash',
        'status',
        'consecutive_failures',
        'recent_success_count',
        'recent_failure_count',
        'last_success_at',
        'last_failure_at',
        'degraded_at',
        'recovered_at',
        'last_alert_at',
    ];

    protected $casts = [
        'last_success_at' => 'datetime',
        'last_failure_at' => 'datetime',
        'degraded_at' => 'datetime',
        'recovered_at' => 'datetime',
        'last_alert_at' => 'datetime',
    ];

    CONST STATUS_HEALTHY = 'healthy';
    CONST STATUS_DEGRADED = 'degraded';
    CONST STATUS_DOWN = 'down';
    CONST STATUS_DISABLED = 'disabled';
}
