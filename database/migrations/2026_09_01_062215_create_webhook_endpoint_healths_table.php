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
        Schema::create('webhook_endpoint_healths', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 50);
            $table->string('legacy_webhook_id', 50);
            $table->string('endpoint_url_hash', 64);
            $table->string('status', 30)->default('healthy');
            $table->unsignedSmallInteger('consecutive_failures')->default(0);
            $table->unsignedSmallInteger('recent_success_count')->default(0);
            $table->unsignedSmallInteger('recent_failure_count')->default(0);
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->timestamp('degraded_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamp('last_alert_at')->nullable();
            $table->timestamps();

            // One health record per tenant endpoint (also serves as the lookup index)
            $table->unique(['tenant_id', 'legacy_webhook_id']);
            $table->index('endpoint_url_hash');
            $table->index(['status', 'last_alert_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoint_healths');
    }
};
