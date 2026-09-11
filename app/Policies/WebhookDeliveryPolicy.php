<?php

namespace App\Policies;

use App\Domain\Webhooks\Models\WebhookEndpointHealth;
use App\Models\User;

class WebhookEndpointHealthPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('endpoint-health.view');
    }

    public function view(User $user, WebhookEndpointHealth $health): bool
    {
        return $user->can('endpoint-health.view')
            && $user->belongsToTenant($health->tenant_id);
    }
}
