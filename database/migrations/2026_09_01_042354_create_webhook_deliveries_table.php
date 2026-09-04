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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            // Prefixed ULID primary key, e.g. dlv_01K0HJW3FMQWQ9 (spec §9.4)
            $table->string('id', 30)->primary();
            $table->string('tenant_id', 50);
            $table->string('webhook_event_id', 30);
            $table->foreign('webhook_event_id')->references('id')->on('webhook_events')->cascadeOnDelete();
            $table->foreignId('endpoint_snapshot_id')->constrained('webhook_endpoint_snapshots')->cascadeOnDelete();
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('first_attempt_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->string('last_error_code', 50)->nullable();
            $table->text('last_error_message')->nullable();
            $table->unsignedInteger('last_response_duration_ms')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_attempt_at']);
            $table->index(['tenant_id', 'created_at']);
            $table->index('endpoint_snapshot_id');
            $table->index('webhook_event_id');
            $table->index('delivered_at');
            $table->index('failed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
