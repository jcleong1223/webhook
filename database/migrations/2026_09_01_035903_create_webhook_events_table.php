<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            // Prefixed ULID primary key, e.g. evt_01K0HJVY8SD6AQN51RN17G9K7A (spec §9.4)
            $table->string('id', 30)->primary();
            $table->string('tenant_id', 50);
            $table->string('tenant_code', 50)->nullable();
            $table->string('merchant_id');
            $table->string('merchant_name', 100)->nullable();
            $table->string('source_system', 50);
            $table->string('source_instance_id', 50);
            $table->string('source_event_id', 100);
            $table->string('legacy_webhook_id', 50);
            $table->string('event_type', 50);
            $table->string('api_version', 20)->default('v1');
            $table->string('aggregate_type', 30);
            $table->string('aggregate_id', 100);
            $table->unsignedInteger('resource_version')->default(1);
            $table->timestamp('occurred_at');
            $table->json('payload');
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index('event_type');
            $table->index(['aggregate_type', 'aggregate_id']);
            $table->index('source_event_id');

            $table->unique(
                ['source_system', 'source_instance_id', 'source_event_id', 'legacy_webhook_id'],
                'webhook_events_idempotency_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
