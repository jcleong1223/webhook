<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'tenants.view',
            'webhook-events.view',
            'deliveries.view',
            'deliveries.cancel',
            'endpoint-health.view',
            'events.redeliver',
            'api-clients.manage',
            'queue-health.view',
            'system-metrics.view',
            'audit-logs.view',
            'payloads.view-sanitized',
            'secrets.view-raw',
            'support-notes.create',
            'retry-rules.manage',
            'webhooks.test',
            'responses.view-sanitized',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Super administrator — everything (also short-circuited by Gate::before).
        Role::findOrCreate('super-administrator', 'web')->syncPermissions($permissions);

        // Support administrator — investigation only; no secrets/credentials/retry rules.
        Role::findOrCreate('support-administrator', 'web')->syncPermissions([
            'webhook-events.view',
            'deliveries.view',
            'payloads.view-sanitized',
            'responses.view-sanitized',
            'events.redeliver',
            'endpoint-health.view',
            'support-notes.create',
        ]);

        // Tenant administrator — scoped to own tenant (enforced in policies).
        Role::findOrCreate('tenant-administrator', 'web')->syncPermissions([
            'webhook-events.view',
            'deliveries.view',
            'endpoint-health.view',
            'events.redeliver',
            'payloads.view-sanitized',
            'responses.view-sanitized',
            'webhooks.test',
        ]);
    }
}
