<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('event')->after('user_id');
            $table->string('ip_address', 45)->nullable()->after('event');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->json('properties')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'event', 'ip_address', 'user_agent', 'properties']);
        });
    }
};
