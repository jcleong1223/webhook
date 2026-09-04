<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class WebhookEvent extends Model
{
    /*** ULID string primary key, e.g. evt_01K0HJVY8SD6AQN51RN17G9K7A (spec §9.4) ***/
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'tenant_code',
        'merchant_id',
        'merchant_name',
        'source_system',
        'source_instance_id',
        'source_event_id',
        'legacy_webhook_id',
        'event_type',
        'api_version',
        'aggregate_type',
        'aggregate_id',
        'resource_version',
        'occurred_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime'
    ];

    protected static function booted(): void
    {
        static::creating(function (self $event) {
            if (empty($event->id)) {
                $event->id = 'evt_' . Str::ulid();
            }
        });
    }

    /****** Relations ******/
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    /*** Latest delivery cycle (manual redelivery creates new cycles — spec §23.1) ***/
    public function delivery(): HasOne
    {
        return $this->hasOne(WebhookDelivery::class)->latestOfMany();
    }
}
