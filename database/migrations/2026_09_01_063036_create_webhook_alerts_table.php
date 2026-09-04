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
        Schema::create('webhook_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 50);
            $table->string('delivery_id', 30)->nullable();
            $table->foreign('delivery_id')->references('id')->on('webhook_deliveries')->nullOnDelete();
            $table->foreignId('endpoint_health_id')
                ->nullable()
                ->constrained('webhook_endpoint_healths')
                ->nullOnDelete();
            $table->string('alert_type', 50);
            $table->string('channel', 50);
            $table->string('recipient');
            $table->string('status', 30)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('deduplication_key');
            $table->timestamps();

            $table->index('deduplication_key');
            $table->index(['tenant_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_alerts');
    }
};
