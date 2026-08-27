<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    Route::get('/keys', [App\Http\Controllers\KeyController::class, 'index'])->name('keys.index');
    Route::get('/keys/create', [App\Http\Controllers\KeyController::class, 'create'])->name('keys.create');
    Route::post('/keys', [App\Http\Controllers\KeyController::class, 'store'])->name('keys.store');
    Route::post('/keys/{key}/ban', [App\Http\Controllers\KeyController::class, 'ban'])->name('keys.ban');
    Route::delete('/keys/{key}', [App\Http\Controllers\KeyController::class, 'destroy'])->name('keys.destroy');

    Route::get('/make-user', function () {
        return response(
            '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create User</title></head>'
            .'<body style="font-family:sans-serif;max-width:420px;margin:40px auto;padding:12px">'
            .'<h2>Create User / Password</h2>'
            .'<form method="post" action="/make-user">'
            .'<input type="hidden" name="_token" value="'.csrf_token().'">'
            .'<p>Username<br><input name="username" required style="width:100%;padding:8px"></p>'
            .'<p>Password<br><input name="password" type="password" required style="width:100%;padding:8px"></p>'
            .'<p>Max uses (0 = unlimited)<br><input name="max_uses" type="number" value="0" min="0" style="width:100%;padding:8px"></p>'
            .'<button type="submit">Create</button></form>'
            .'<p><a href="/keys">Back to keys</a></p></body></html>'
        );
    });

    Route::post('/make-user', function (Request $request) {
        $user = trim((string) $request->input('username'));
        $pass = (string) $request->input('password');
        $max  = (int) $request->input('max_uses', 0);
        if ($user === '' || $pass === '') {
            return 'Username/password empty';
        }
        if (!Schema::hasTable('keys')) {
            return 'Open API once first, then retry';
        }
        if (!Schema::hasColumn('keys', 'username')) {
            Schema::table('keys', function ($t) {
                $t->string('username')->nullable();
                $t->string('password')->nullable();
            });
        }
        $row = new \App\Models\Key();
        $row->key = strtoupper(substr(md5($user.time()), 0, 4).'-'.substr(md5($user), 0, 4).'-USER-PASS');
        $row->username = $user;
        $row->password = Hash::make($pass);
        $row->status = 'active';
        $row->max_uses = $max;
        $row->used_count = 0;
        $row->save();
        $lim = $max === 0 ? 'unlimited' : (string) $max;
        return 'Created: '.$user.' (max: '.$lim.') <a href="/make-user">Add another</a> | <a href="/keys">Keys</a>';
    });
});

// ---- Admin boot (ek baar) ----

// ---- Clean login (HTML only, no blade) ----
Route::get('/login', function () {
    $err = session('login_error', '');
    $box = $err !== ''
        ? '<div style="background:#3b1111;color:#fca5a5;padding:10px;border-radius:8px;margin-bottom:12px">'.htmlspecialchars($err).'</div>'
        : '';
    return response(
        '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        .'<title>Login - DS Gaming</title></head>'
        .'<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0f0f;font-family:system-ui,sans-serif">'
        .'<div style="background:#1a1a1a;color:#fff;padding:28px;border-radius:14px;width:92%;max-width:360px;box-shadow:0 8px 30px rgba(0,0,0,.4)">'
        .'<h1 style="margin:0 0 6px;text-align:center;font-size:22px">DS Gaming</h1>'
        .'<p style="margin:0 0 20px;text-align:center;color:#888;font-size:13px">Admin Panel</p>'
        .$box
        .'<form method="POST" action="/do-login">'
        .'<input type="hidden" name="_token" value="'.csrf_token().'">'
        .'<label style="font-size:12px;color:#aaa">Email</label>'
        .'<input type="email" name="email" required value="das@admin.com" '
        .'style="width:100%;box-sizing:border-box;margin:6px 0 14px;padding:11px;border-radius:8px;border:1px solid #333;background:#111;color:#fff">'
        .'<label style="font-size:12px;color:#aaa">Password</label>'
        .'<input type="password" name="password" required placeholder="Password" '
        .'style="width:100%;box-sizing:border-box;margin:6px 0 18px;padding:11px;border-radius:8px;border:1px solid #333;background:#111;color:#fff">'
        .'<button type="submit" style="width:100%;padding:12px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:600;font-size:15px">Sign in</button>'
        .'</form></div></body></html>'
    );
})->middleware('guest')->name('login');


Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::post('/do-login', function (\Illuminate\Http\Request $request) {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            \Illuminate\Support\Facades\Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->remember_token();
                $table->timestamps();
            });
        }
        if (\App\Models\User::count() === 0) {
            \App\Models\User::create([
                'name' => 'DS Gaming',
                'email' => 'das@admin.com',
                'password' => \Illuminate\Support\Facades\Hash::make('DsGame2026'),
                'email_verified_at' => now(),
            ]);
        }
        $email = trim((string) $request->input('email'));
        $pass  = (string) $request->input('password');
        $u = \App\Models\User::where('email', $email)->first();
        if (!$u || !\Illuminate\Support\Facades\Hash::check($pass, $u->password)) {
            return redirect('/login')->with('login_error', 'Email ya password galat');
        }
        \Illuminate\Support\Facades\Auth::login($u, true);
        $request->session()->regenerate();
        return view('dashboard');
    } catch (\Throwable $e) {
        return redirect('/login')->with('login_error', 'Error');
    }
});
