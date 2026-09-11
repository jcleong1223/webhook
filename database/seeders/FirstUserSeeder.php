<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $super = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'super@example.com')],
            ['name' => 'Super Admin', 'password' => env('ADMIN_PASSWORD', 'passwordsuper')]
        );
        $super->syncRoles('super-administrator');

        // Support admin — tenant_id null (all tenants, read/investigate).
        $support = User::firstOrCreate(
            ['email' => env('SUPPORT_EMAIL', 'support@example.com')],
            ['name' => 'Support Admin', 'password' => env('SUPPORT_PASSWORD', 'passwordsupport')]
        );
        $support->syncRoles('support-administrator');

        // Tenant admin — scoped to one tenant_id (match a real webhook_events.tenant_id).
        $tenant = User::firstOrCreate(
            ['email' => env('TENANT_EMAIL', 'tenant@example.com')],
            [
                'name'      => 'Tenant Admin',
                'password'  => env('TENANT_PASSWORD', 'passwordtenant'),
            ]
        );
        $tenant->syncRoles('tenant-administrator');
    }
}
