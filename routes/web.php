

// ===== Real login fix =====
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
        $ok = \Illuminate\Support\Facades\Hash::check($pass, $u->password);
        return response(
            '<pre>Admin ready for LOGIN PAGE

Email: das@admin.com
Password: DsGame2026
Hash: '.($ok ? 'OK' : 'FAIL').'

Ab jao: /login
(yeh /boot-admin page baad mein hata dena)
</pre>',
            200,
            ['Content-Type' => 'text/html']
        );
    } catch (\Throwable $e) {
        return 'ERROR: '.$e->getMessage();
    }
});

// Custom login POST (Fortify fail ho to yeh kaam kare)
Route::post('/do-login', function (\Illuminate\Http\Request $request) {
    $email = trim((string) $request->input('email'));
    $pass  = (string) $request->input('password');
    $u = \App\Models\User::where('email', $email)->first();
    if (!$u || !\Illuminate\Support\Facades\Hash::check($pass, $u->password)) {
        return redirect('/login')->withErrors(['email' => 'Email ya password galat.']);
    }
    \Illuminate\Support\Facades\Auth::login($u, $request->boolean('remember'));
    $request->session()->regenerate();
    return redirect('/keys');
});
