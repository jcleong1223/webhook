<?php

namespace App\Providers;

use App\Domain\Webhooks\Contracts\WebhookSigner;
use App\Domain\Webhooks\Contracts\WebhookTransport;
use App\Domain\Webhooks\Services\HmacWebhookSigner;
use App\Domain\Webhooks\Services\HttpWebhookTransport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Webhook delivery contracts (spec §30)
        $this->app->bind(WebhookSigner::class, HmacWebhookSigner::class);
        $this->app->bind(WebhookTransport::class, HttpWebhookTransport::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::before(fn($user, $ability) => $user?->hasRole('super-administrator') ? true : null);

        Gate::policy(WebhookEvent::class, WebhookEventPolicy::class);
        Gate::policy(WebhookDelivery::class, WebhookDeliveryPolicy::class);
        Gate::policy(WebhookEndpointHealth::class, WebhookEndpointHealthPolicy::class);
    }
}
