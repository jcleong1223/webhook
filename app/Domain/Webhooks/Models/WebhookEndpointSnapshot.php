<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookEndpointSnapshot extends Model
{
    protected $fillable = [
        'tenant_id',
        'legacy_webhook_id',
        'name',
        'url',
        'signing_secret_encrypted',
        'timeout_seconds',
        'max_attempts',
        'status',
        'alert_configuration',
        'configuration_hash',
    ];

    protected $hidden = [
        'signing_secret_encrypted',
    ];

    protected $casts = [
        'signing_secret_encrypted' => 'encrypted',
        'alert_configuration' => 'array',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class, 'endpoint_snapshot_id');
    }
}
