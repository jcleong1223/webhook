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
        'internal_api_client_id',
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
    // public function deliveries(): HasMany
    // {
    //     return $this->hasMany(WebhookDelivery::class);
    // }

    /*** Latest delivery cycle (manual redelivery creates new cycles — spec §23.1) ***/
    public function delivery(): HasOne
    {
        return $this->hasOne(WebhookDelivery::class)->latestOfMany();
    }

    public function webhookDelivery(): HasOne
    {
        return $this->hasOne(WebhookDelivery::class);
    }
}




// {
// "source": {
//     "system": "chicha-backoffice",
//     "instance_id": "chicha",
//     "event_id": "chicha-user-123-registered",
//     "occurred_at": "2026-07-24T06:20:15.312Z"
//     "callback_url": "https://chicha.datapos.sg/webhooks/orders"
//     "secret": "whsec_xxxxxxxxx"
// },
// "tenant": {
//     "id": "chicha_zis7zmhgow2cm85aileiwv0im0ycknhs",
//     "code": "ChichaSG"
// },
// "event": {
//     "type": "user.registered",
//     "api_version": "v1",
//     "aggregate_type": "user",
//     "aggregate_id": "123",
//     "resource_version": 1
// },
// "litecard_config": {
//     "username": "Merchant ERP",
//     "encrypted_password": "xxxxxxxxxx",
//     "url": "https://bff-api.enterprise.litecard.io/api/v1/",
//     "signing_secret": "litecard_xxxxxxxxx",
//     "timeout_seconds": 10,
//     "max_attempts": 8,
//     "status": "active"
// },
// "payload": {
//     "litecard": {
//         "template_id": "template_123",
//         "name": "card display name",
//         "description": "card description"
//         "businessName": "Chicha membership name",
//         "colours": {
//             "background": "#ffffff",
//             "label": "#000000"
//             "foreground": "#000000"
//         },
//         "barcode": "039873932845723",
//         "images": {
//             "logo": "https://example.com/logo.png",
//             "icon": "https://example.com/background.png"
//         }
//     }
//     "user": {
//         "id": "123",
//         "firstname": "John",
//         "lastname": "Doe",
//         "email": "user@example.com",
//         "business_date": "2026-07-24",
//         "status": "confirmed",
//     }
// }
// }


// CUrrent stage.
1. Start testing