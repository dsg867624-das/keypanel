<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use App\Models\Key;

function dsEnsure(): void {
    if (!Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->remember_token();
            $table->timestamps();
        });
    }
    if (!Schema::hasTable('keys')) {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('status')->default('active');
            $table->unsignedInteger('duration')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->default(0);
            $table->unsignedInteger('used_count')->default(0);
            $table->text('note')->nullable();
            $table->string('username')->nullable()->index();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    } else {
        Schema::table('keys', function (Blueprint $table) {
            if (!Schema::hasColumn('keys', 'username')) {
                $table->string('username')->nullable()->index();
            }
            if (!Schema::hasColumn('keys', 'password')) {
                $table->string('password')->nullable();
            }
        });
    }
    if (\App\Models\User::count() === 0) {
        \App\Models\User::create([
            'name' => 'DS Gaming',
            'email' => 'das@admin.com',
            'password' => Hash::make('8810737340@@##$$DSGAMING'),
            'email_verified_at' => now(),
        ]);
    }
}

Route::post('/key-check', function (Request $request) {
    dsEnsure();
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
    $max = $row->max_uses;
    if ($max !== null && (int)$max > 0 && (int)$row->used_count >= (int)$max) {
        return response()->json(['status' => 'error', 'message' => 'limit'], 401);
    }
    return response()->json(['status' => 'ok', 'message' => 'success']);
});