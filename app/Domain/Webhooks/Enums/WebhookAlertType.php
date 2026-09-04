<?php

namespace App\Domain\Webhooks\Enums;

enum WebhookAlertType: string
{
    case DELIVERY_WARNING = 'delivery_warning';
    case DELIVERY_PERMANENT_FAILURE = 'delivery_permanent_failure';
    case ENDPOINT_DEGRADED = 'endpoint_degraded';
    case ENDPOINT_DOWN = 'endpoint_down';
    case ENDPOINT_RECOVERED = 'endpoint_recovered';

    public function label(): string
    {
        return match ($this) {
            self::DELIVERY_WARNING => 'Delivery warning',
            self::DELIVERY_PERMANENT_FAILURE => 'Delivery permanent failure',
            self::ENDPOINT_DEGRADED => 'Endpoint degraded',
            self::ENDPOINT_DOWN => 'Endpoint down',
            self::ENDPOINT_RECOVERED => 'Endpoint recovered',
        };
    }
}