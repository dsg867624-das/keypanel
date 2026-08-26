<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Key;

Route::post('/key-check', function (Request $request) {
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
