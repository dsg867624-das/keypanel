<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});





Route::middleware('auth')->get('/make-user', function () {
    return '<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create User</title></head>
    <body style="font-family:sans-serif;max-width:420px;margin:40px auto;padding:12px">
    <h2>Create User / Password</h2>
    <form method="post" action="/make-user">
      <input type="hidden" name="_token" value="'.csrf_token().'">
      <p>Username<br><input name="username" required style="width:100%;padding:8px"></p>
      <p>Password<br><input name="password" type="password" required style="width:100%;padding:8px"></p>
      <p>Max uses (0 = unlimited)<br>
         <input name="max_uses" type="number" value="0" min="0" max="999999" style="width:100%;padding:8px"></p>
      <button type="submit" style="padding:10px 16px;margin-top:8px">Create</button>
    </form>
    <p style="margin-top:16px"><a href="/keys">Back to keys</a></p>
    </body></html>';
});

Route::middleware('auth')->post('/make-user', function (\Illuminate\Http\Request $request) {
    $user = trim((string) $request->input('username'));
    $pass = (string) $request->input('password');
    $max  = (int) $request->input('max_uses', 0);
    if ($user === '' || $pass === '') {
        return 'Username/password empty';
    }
    if (!\Illuminate\Support\Facades\Schema::hasTable('keys')) {
        return 'keys table missing — open /api/key-check once then retry';
    }
    if (!\Illuminate\Support\Facades\Schema::hasColumn('keys', 'username')) {
        \Illuminate\Support\Facades\Schema::table('keys', function ($t) {
            $t->string('username')->nullable();
            $t->string('password')->nullable();
        });
    }
    $row = new \App\Models\Key();
    $row->key = strtoupper(substr(md5($user.time()), 0, 4).'-'.substr(md5($user), 0, 4).'-USER-PASS');
    $row->username = $user;
    $row->password = \Illuminate\Support\Facades\Hash::make($pass);
    $row->status = 'active';
    $row->max_uses = $max; // 0 = unlimited
    $row->used_count = 0;
    $row->save();
    $lim = $max === 0 ? 'unlimited' : (string)$max;
    return 'Created user: '.$user.' (max uses: '.$lim.') — use USER+PASS in app. <a href="/make-user">Add another</a> | <a href="/keys">Keys</a>';
});

Route::get('/setup-admin', function () {
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

        $email = 'das@admin.com';
        $pass  = 'DsGame2026';

        // purana user hatao
        \App\Models\User::where('email', $email)->delete();

        $u = \App\Models\User::create([
            'name' => 'DS Gaming',
            'email' => $email,
            'password' => bcrypt($pass),
            'email_verified_at' => now(),
        ]);

        $ok = \Illuminate\Support\Facades\Hash::check($pass, $u->password);

        return response(
            '<h2>Admin READY</h2>'
            .'<p>Email: <b>das@admin.com</b></p>'
            .'<p>Password: <b>DsGame2026</b></p>'
            .'<p>Hash check: '.($ok ? 'OK' : 'FAIL').'</p>'
            .'<p><a href="/login">Go to Login</a></p>',
            200,
            ['Content-Type' => 'text/html']
        );
    } catch (\Throwable $e) {
        return 'ERROR: '.$e->getMessage();
    }
});
