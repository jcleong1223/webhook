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
            'user.view',
            'user.view-any',
            'user.create',
            'user.update',
            'user.update-any',
            'user.delete',
            'role.view',
            'role.view-any',
            'role.create',
            'role.update',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.delete',
            'monitoring.view',
            'monitoring.view-any',
            'monitoring.manage',
            'metric.view',
            'metric.view-any',
            'batch.view',
            'batch.view-any',
            'pending-job.view',
            'pending-job.view-any',
            'completed-job.view',
            'completed-job.view-any',
            'silenced-job.view',
            'silenced-job.view-any',
            'failed-job.view',
            'failed-job.view-any',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Super administrator — everything (also short-circuited by Gate::before).
        Role::findOrCreate('super-administrator', 'web');

        // Support administrator — investigation only; no secrets/credentials/retry rules.
        Role::findOrCreate('support-administrator', 'web')->syncPermissions([
            'user.view',
            'user.update',
            'monitoring.view',
            'metric.view',
            'batch.view',
            'pending-job.view',
            'completed-job.view',
            'failed-job.view',
            'silenced-job.view',
        ]);

        // Tenant administrator — scoped to own tenant (enforced in policies).
        Role::findOrCreate('tenant-administrator', 'web')->syncPermissions([
            'user.view',
            'user.update',
            'monitoring.view',
            'metric.view',
            'batch.view',
            'pending-job.view',
            'completed-job.view',
            'failed-job.view',
            'silenced-job.view',
        ]);
    }
}
