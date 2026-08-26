

// ===== Real login fix =====

// Custom login POST (Fortify fail ho to yeh kaam kare)

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/boot-admin', function () {
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
        \App\Models\User::where('email', $email)->delete();
        $u = \App\Models\User::create([
            'name' => 'DS Gaming',
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($pass),
            'email_verified_at' => now(),
        ]);
        $ok = \Illuminate\Support\Facades\Hash::check($pass, $u->password) ? 'OK' : 'FAIL';
        return response(
            '<!DOCTYPE html><html><body style="font-family:sans-serif;max-width:420px;margin:40px auto">'
            .'<h2>Admin ready</h2>'
            .'<p>Email: <b>das@admin.com</b></p>'
            .'<p>Password: <b>DsGame2026</b></p>'
            .'<p>Hash: '.$ok.'</p>'
            .'<p><a href="/login">Open Login Page</a></p>'
            .'</body></html>'
        );
    } catch (\Throwable $e) {
        return 'ERROR: '.$e->getMessage();
    }
});

Route::get('/login', function () {
    $err = session('login_error', '');
    $errHtml = $err !== ''
        ? '<div style="background:#fee;color:#c00;padding:10px;border-radius:8px;margin-bottom:12px">'.htmlspecialchars($err).'</div>'
        : '';
    $html = '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title></head>'
        .'<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#111;font-family:sans-serif">'
        .'<div style="background:#1e1e1e;color:#fff;padding:28px;border-radius:12px;width:100%;max-width:360px">'
        .'<h1 style="margin:0 0 8px;text-align:center">DS Gaming</h1>'
        .'<p style="margin:0 0 20px;text-align:center;color:#aaa;font-size:14px">Admin Login</p>'
        .$errHtml
        .'<form method="POST" action="/do-login">'
        .'<input type="hidden" name="_token" value="'.csrf_token().'">'
        .'<label style="font-size:13px;color:#ccc">Email</label>'
        .'<input type="email" name="email" required placeholder="das@admin.com" '
        .'style="width:100%;box-sizing:border-box;margin:6px 0 14px;padding:10px;border-radius:8px;border:1px solid #444;background:#2a2a2a;color:#fff">'
        .'<label style="font-size:13px;color:#ccc">Password</label>'
        .'<input type="password" name="password" required placeholder="Password" '
        .'style="width:100%;box-sizing:border-box;margin:6px 0 18px;padding:10px;border-radius:8px;border:1px solid #444;background:#2a2a2a;color:#fff">'
        .'<button type="submit" style="width:100%;padding:12px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:600;font-size:15px">Sign in</button>'
        .'</form></div></body></html>';
    return response($html);
})->middleware('guest')->name('login');

Route::post('/do-login', function (\Illuminate\Http\Request $request) {
    $email = trim((string) $request->input('email'));
    $pass  = (string) $request->input('password');
    $u = \App\Models\User::where('email', $email)->first();
    if (!$u || !\Illuminate\Support\Facades\Hash::check($pass, $u->password)) {
        return redirect('/login')->with('login_error', 'Email ya password galat');
    }
    \Illuminate\Support\Facades\Auth::login($u, true);
    $request->session()->regenerate();
    return redirect('/keys');
});
