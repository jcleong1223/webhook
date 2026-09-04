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
        Schema::create('webhook_delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 50);
            $table->string('delivery_id', 30);
            $table->foreign('delivery_id')->references('id')->on('webhook_deliveries')->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->timestamp('request_timestamp');
            $table->json('request_headers_sanitized');
            $table->string('request_body_hash', 100);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_headers_sanitized')->nullable();
            $table->text('response_body_excerpt')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('error_type', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            $table->index(['delivery_id', 'attempt_number']);
            $table->index(['tenant_id', 'attempted_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_delivery_attempts');
    }
};
