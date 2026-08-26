<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use App\Models\Key;

function dsEnsureTables(): void {
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
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    } else {
        if (!Schema::hasColumn('keys', 'username')) {
            Schema::table('keys', function (Blueprint $table) {
                $table->string('username')->nullable();
            });
        }
        if (!Schema::hasColumn('keys', 'password')) {
            Schema::table('keys', function (Blueprint $table) {
                $table->string('password')->nullable();
            });
        }
    }
}

Route::post('/key-check', function (Request $request) {
    dsEnsureTables();
    $user = trim((string) $request->input('user', $request->input('username', '')));
    $pass = (string) $request->input('pass', $request->input('password', ''));
    $code = trim((string) $request->input('key', ''));

    $row = null;
    if ($user !== '' && $pass !== '') {
        $row = Key::where('username', $user)->first();
        if (!$row || empty($row->password) || !Hash::check($pass, $row->password)) {
            return response()->json(['status' => 'error', 'message' => 'invalid'], 401);
        }
    } elseif ($code !== '') {
        $row = Key::where('key', $code)->first();
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'invalid'], 401);
        }
    } else {
        return response()->json(['status' => 'error', 'message' => 'missing login'], 422);
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
