<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use App\Models\Key;

function dsEnsure(): void {
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
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('display_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_id')->nullable();
            $table->string('android_version')->nullable();
            $table->string('app_version')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    } else {
        $cols = [
            'username' => 'string',
            'password' => 'string',
            'display_name' => 'string',
            'phone' => 'string',
            'device_name' => 'string',
            'device_id' => 'string',
            'android_version' => 'string',
            'app_version' => 'string',
            'ip_address' => 'string',
            'last_login_at' => 'timestamp',
        ];
        foreach ($cols as $col => $type) {
            if (!Schema::hasColumn('keys', $col)) {
                Schema::table('keys', function (Blueprint $table) use ($col, $type) {
                    if ($type === 'timestamp') {
                        $table->timestamp($col)->nullable();
                    } else {
                        $table->string($col)->nullable();
                    }
                });
            }
        }
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

    // App se aaya data save
    $row->display_name = $request->input('name', $row->display_name);
    $row->phone = $request->input('phone', $row->phone);
    $row->device_name = $request->input('device_name', $row->device_name);
    $row->device_id = $request->input('device_id', $row->device_id);
    $row->android_version = $request->input('android_version', $row->android_version);
    $row->app_version = $request->input('app_version', $row->app_version);
    $row->ip_address = $request->ip();
    $row->last_login_at = now();
    $row->used_count = (int)$row->used_count + 1;
    $row->save();

    return response()->json([
        'status' => 'ok',
        'message' => 'success',
        'user' => $row->username ?: $row->key,
    ]);
});