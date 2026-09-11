<?php

namespace App\Policies;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Models\User;

class WebhookDeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('deliveries.view');
    }

    public function view(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('deliveries.view')
            && $user->belongsToTenant($delivery->tenant_id);
    }

    public function redeliver(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('events.redeliver')
            && $user->belongsToTenant($delivery->tenant_id);
    }

    public function cancel(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('deliveries.cancel')
            && $user->belongsToTenant($delivery->tenant_id);
    }

    public function viewSanitizedPayload(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('payloads.view-sanitized')
            && $user->belongsToTenant($delivery->tenant_id);
    }

    public function viewRawSecrets(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('secrets.view-raw')
            && $user->belongsToTenant($delivery->tenant_id);
    }
}
