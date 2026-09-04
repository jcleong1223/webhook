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
        Schema::create('webhook_endpoint_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 50);
            $table->string('legacy_webhook_id', 50);
            $table->string('name', 100);
            $table->text('url');
            $table->text('signing_secret_encrypted');
            $table->unsignedSmallInteger('timeout_seconds')->default(10);
            $table->unsignedTinyInteger('max_attempts')->default(8);
            $table->string('status', 50);
            $table->json('alert_configuration')->nullable();
            $table->string('configuration_hash', 100);

            $table->timestamps();

            $table->index(['tenant_id', 'legacy_webhook_id']);
            $table->index('configuration_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoint_snapshots');
    }
};
