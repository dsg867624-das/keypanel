<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            $table->string('key_hash', 64)->unique();

            $table->string('name')->nullable();

            $table->enum('status', [
                'active',
                'revoked',
                'expired'
            ])->default('active');

            $table->timestamp('expires_at')->nullable();

            $table->unsignedInteger('activation_limit')->default(1);

            $table->unsignedInteger('activation_count')->default(0);

            $table->timestamps();

            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

