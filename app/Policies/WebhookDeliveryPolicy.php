<?php

namespace App\Policies;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Models\User;

class WebhookDeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('webhook-delivery.view');
    }

    public function view(User $user, WebhookDelivery $delivery): bool
    {
        return $user->can('webhook-delivery.view')
            && $user->belongsToTenant($delivery->tenant_id);
    }

    public function redeliver(User $user): bool
    {
        return $user->can('webhook-delivery.redeliver');
    }

    public function cancel(User $user): bool
    {
        return $user->can('webhook-delivery.cancel');
    }

    // public function viewSanitizedPayload(User $user, WebhookDelivery $delivery): bool
    // {
    //     return $user->can('payloads.view-sanitized')
    //         && $user->belongsToTenant($delivery->tenant_id);
    // }

    // public function viewRawSecrets(User $user, WebhookDelivery $delivery): bool
    // {
    //     return $user->can('secrets.view-raw')
    //         && $user->belongsToTenant($delivery->tenant_id);
    // }
}
