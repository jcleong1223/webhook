<?php

namespace App\Domain\Webhooks\Enums;

enum EndpointHealthStatus: string
{
    case HEALTHY = 'healthy';
    case DEGRADED = 'degraded';
    case DOWN = 'down';
    case DISABLED = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::HEALTHY => 'Healthy',
            self::DEGRADED => 'Degraded',
            self::DOWN => 'Down',
            self::DISABLED => 'Disabled',
        };
    }
}
