<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $hidden = [
        'endpoint_url_hash'
    ];

    public static $dateColumns = [
        'created_at' => [
            'label' => 'Created At',
            'format' => 'Y-m-d H:i:s',
        ],
        'degraded_at' => [
            'label' => 'Degraded At',
            'format' => 'Y-m-d H:i:s',
        ],
        'last_alert_at' => [
            'label' => 'Last Alert At',
            'format' => 'Y-m-d H:i:s',
        ],
        'last_failure_at' => [
            'label' => 'Last Failure At',
            'format' => 'Y-m-d H:i:s',
        ],
        'last_success_at' => [
            'label' => 'Last Success At',
            'format' => 'Y-m-d H:i:s',
        ],
        'recovered_at' => [
            'label' => 'Recovered At',
            'format' => 'Y-m-d H:i:s',
        ],

    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(
            WebhookEndpointSnapshot::class,
            'legacy_webhook_id',
            'legacy_webhook_id'
        );
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(
            WebhookEndpointSnapshot::class,
            'legacy_webhook_id',
            'legacy_webhook_id'
        )->where('tenant_id', $this->tenant_id)
        ->latestOfMany();
    }
}
