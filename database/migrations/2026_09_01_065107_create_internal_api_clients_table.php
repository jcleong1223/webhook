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
        Schema::create('internal_api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 50)->unique();
            $table->string('name', 100);
            $table->text('secret_encrypted');
            $table->json('allowed_ips')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_api_clients');
    }
};
