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
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID as the primary key
            $table->string('email')->unique(); // Unique index on email
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Add expiry time for the token
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
