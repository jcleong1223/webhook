<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookAlert extends Model
{
    protected $fillable = [
        'tenant_id',
        'delivery_id',
        'endpoint_health_id',
        'alert_type',
        'channel',
        'recipient',
        'status',
        'sent_at',
        'error_message',
        'deduplication_key',
    ];

    CONST ALERT_TYPE_DELIVERY_WARNING = 'delivery_warning';
    CONST ALERT_TYPE_DELIVERY_PERMANENT_FAIL = 'delivery_permanent_failure';
    CONST ALERT_TYPE_ENDPOINT_DEGRADED = 'endpoint_degraded';
    CONST ALERT_TYPE_ENDPOINT_DOWN = 'endpoint_down';
    CONST ALERT_TYPE_ENDPOINT_RECOVERED = 'endpoint_recovered';
}
