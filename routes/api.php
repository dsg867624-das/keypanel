<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Key;

Route::post('/key-check', function (Request $request) {
    if (!Schema::hasTable('keys')) {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('status')->default('active');
            $table->unsignedInteger('duration')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    $code = trim((string) $request->input('key', ''));
    if ($code === '') {
        return response()->json(['status' => 'error', 'message' => 'missing key'], 422);
    }

    $row = Key::where('key', $code)->first();
    if (!$row) {
        return response()->json(['status' => 'error', 'message' => 'invalid'], 401);
    }
    if (method_exists($row, 'isExpired') && $row->isExpired()) {
        return response()->json(['status' => 'error', 'message' => 'expired'], 401);
    }
    $st = strtolower(trim((string) ($row->status ?? 'active')));
    if (in_array($st, ['banned', 'disabled', 'revoked', 'inactive'], true)) {
        return response()->json(['status' => 'error', 'message' => 'banned'], 401);
    }
    return response()->json(['status' => 'ok', 'message' => 'success']);
});
