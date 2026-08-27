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

    
// ---- Admin boot (ek baar) ----

// ---- Clean login (HTML only, no blade) ----

// Browser se seedha logout (GET allowed)
Route::get('/exit', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});

Route::match(['get', 'post'], '/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    $err = session('login_error', '');
    $box = $err !== ''
        ? '<div class="err">'.htmlspecialchars($err).'</div>'
        : '';
    return response('<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>DS Gaming Login</title>
<style>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap");
*{box-sizing:border-box;margin:0;padding:0}
body{
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  font-family:Outfit,system-ui,sans-serif;color:#fff;
  background:#050505;
  background-image:
    radial-gradient(ellipse 80% 50% at 50% -20%,rgba(220,38,38,.35),transparent),
    radial-gradient(ellipse 60% 40% at 100% 100%,rgba(127,29,29,.2),transparent),
    radial-gradient(ellipse 50% 30% at 0% 80%,rgba(69,10,10,.25),transparent);
}
.wrap{width:92%;max-width:420px;position:relative;z-index:1}
.brand{text-align:center;margin-bottom:32px}
.brand .logo{
  width:64px;height:64px;margin:0 auto 16px;border-radius:18px;
  background:linear-gradient(135deg,#ef4444,#7f1d1d);
  display:flex;align-items:center;justify-content:center;
  font-size:28px;font-weight:800;box-shadow:0 12px 40px rgba(239,68,68,.45);
}
.brand h1{font-size:26px;font-weight:800;letter-spacing:.08em}
.brand h1 b{color:#f87171}
.brand p{color:#737373;font-size:13px;margin-top:8px;font-weight:400}
.card{
  background:rgba(15,15,15,.85);backdrop-filter:blur(20px);
  border:1px solid rgba(255,255,255,.08);border-radius:24px;
  padding:32px 28px;
  box-shadow:0 25px 80px rgba(0,0,0,.6),inset 0 1px 0 rgba(255,255,255,.06);
}
.err{
  background:rgba(127,29,29,.4);border:1px solid #991b1b;color:#fecaca;
  padding:12px 14px;border-radius:12px;margin-bottom:18px;font-size:13px;
}
label{display:block;font-size:11px;color:#a3a3a3;margin-bottom:8px;
  text-transform:uppercase;letter-spacing:.1em;font-weight:600}
.field{margin-bottom:18px}
input{
  width:100%;padding:14px 16px;border-radius:14px;
  border:1px solid #262626;background:#0a0a0a;color:#fff;
  font-size:15px;font-family:inherit;outline:none;transition:.2s;
}
input:focus{border-color:#ef4444;box-shadow:0 0 0 4px rgba(239,68,68,.15)}
input::placeholder{color:#525252}
button{
  width:100%;padding:15px;border:0;border-radius:14px;margin-top:8px;
  font-size:15px;font-weight:700;font-family:inherit;cursor:pointer;color:#fff;
  background:linear-gradient(180deg,#f87171 0%,#dc2626 45%,#991b1b 100%);
  box-shadow:0 10px 30px rgba(220,38,38,.4),inset 0 1px 0 rgba(255,255,255,.2);
  transition:.15s;
}
button:hover{transform:translateY(-1px);filter:brightness(1.08)}
button:active{transform:translateY(0)}
.foot{text-align:center;margin-top:24px;font-size:11px;color:#404040;letter-spacing:.04em}
</style></head><body>
<div class="wrap">
  <div class="brand">
    <div class="logo">DS</div>
    <h1><b>DS</b> GAMING</h1>
    <p>Premium Admin Access</p>
  </div>
  <div class="card">'.$box.'
    <form method="POST" action="/do-login">
      <input type="hidden" name="_token" value="'.csrf_token().'">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required value="das@admin.com" autocomplete="username" placeholder="admin@email.com">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter password" autocomplete="current-password">
      </div>
      <button type="submit">Sign In</button>
    </form>
  </div>
  <div class="foot">SECURE PANEL · DS GAMING</div>
</div>
</body></html>');
})->name('login');

Route::get('/login', function () {
    if (function_exists('auth') && auth()->check()) {
        return redirect('/dashboard');
    }
    $err = session('login_error', '');
    $box = $err !== '' ? '<div class="err">'.htmlspecialchars($err).'</div>' : '';
    return response('<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>DS Gaming Login</title>
<style>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap");
*{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:Outfit,system-ui,sans-serif;color:#fff;background:#050505;
background-image:radial-gradient(ellipse 80% 50% at 50% -20%,rgba(220,38,38,.35),transparent),radial-gradient(ellipse 60% 40% at 100% 100%,rgba(127,29,29,.2),transparent)}
.wrap{width:92%;max-width:420px}
.brand{text-align:center;margin-bottom:32px}
.brand .logo{width:64px;height:64px;margin:0 auto 16px;border-radius:18px;background:linear-gradient(135deg,#ef4444,#7f1d1d);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;box-shadow:0 12px 40px rgba(239,68,68,.45)}
.brand h1{font-size:26px;font-weight:800;letter-spacing:.08em}
.brand h1 b{color:#f87171}
.brand p{color:#737373;font-size:13px;margin-top:8px}
.card{background:rgba(15,15,15,.85);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:32px 28px;box-shadow:0 25px 80px rgba(0,0,0,.6)}
.err{background:rgba(127,29,29,.4);border:1px solid #991b1b;color:#fecaca;padding:12px 14px;border-radius:12px;margin-bottom:18px;font-size:13px}
label{display:block;font-size:11px;color:#a3a3a3;margin-bottom:8px;text-transform:uppercase;letter-spacing:.1em;font-weight:600}
.field{margin-bottom:18px}
input{width:100%;padding:14px 16px;border-radius:14px;border:1px solid #262626;background:#0a0a0a;color:#fff;font-size:15px;font-family:inherit;outline:none}
input:focus{border-color:#ef4444;box-shadow:0 0 0 4px rgba(239,68,68,.15)}
button{width:100%;padding:15px;border:0;border-radius:14px;margin-top:8px;font-size:15px;font-weight:700;font-family:inherit;cursor:pointer;color:#fff;
background:linear-gradient(180deg,#f87171,#dc2626 45%,#991b1b);box-shadow:0 10px 30px rgba(220,38,38,.4)}
.foot{text-align:center;margin-top:24px;font-size:11px;color:#404040}
</style></head><body><div class="wrap">
<div class="brand"><div class="logo">DS</div><h1><b>DS</b> GAMING</h1><p>Premium Admin Access</p></div>
<div class="card">'.$box.'
<form method="POST" action="/do-login">
<input type="hidden" name="_token" value="'.csrf_token().'">
<div class="field"><label>Email</label>
<input type="email" name="email" required value="das@admin.com" autocomplete="username"></div>
<div class="field"><label>Password</label>
<input type="password" name="password" required placeholder="Enter password" autocomplete="current-password"></div>
<button type="submit">Sign In</button>
</form></div>
<div class="foot">SECURE PANEL · DS GAMING</div>
</div></body></html>');
})->name('login');

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
        return redirect('/dashboard');
    } catch (\Throwable $e) {
        return redirect('/login')->with('login_error', 'Error: '.$e->getMessage());
    }
});

Route::middleware('auth')->get('/make-user', function () {
    $ok = session('success', '');
    $err = session('error', '');
    $msg = '';
    if ($ok !== '') $msg = '<div class="ok">'.htmlspecialchars($ok).'</div>';
    if ($err !== '') $msg = '<div class="err">'.htmlspecialchars($err).'</div>';
    $token = csrf_token();
    return response('<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create User — DS Gaming</title>
<style>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap");
*{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;font-family:Outfit,system-ui,sans-serif;color:#fafafa;background:#050505;
background-image:radial-gradient(ellipse 70% 45% at 50% -10%,rgba(220,38,38,.3),transparent),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(127,29,29,.15),transparent)}
.top{max-width:480px;margin:0 auto;padding:20px 16px 0;display:flex;justify-content:space-between}
.top a{color:#a3a3a3;text-decoration:none;font-size:13px}
.wrap{max-width:420px;margin:40px auto;padding:0 16px 40px}
.brand{text-align:center;margin-bottom:28px}
.logo{width:56px;height:56px;margin:0 auto 14px;border-radius:16px;background:linear-gradient(135deg,#ef4444,#7f1d1d);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;box-shadow:0 10px 32px rgba(239,68,68,.4)}
h1{font-size:24px;font-weight:800}
.sub{color:#737373;font-size:13px;margin-top:6px}
.card{background:rgba(15,15,15,.9);border:1px solid rgba(255,255,255,.08);border-radius:22px;padding:28px 24px;box-shadow:0 25px 60px rgba(0,0,0,.55)}
label{display:block;font-size:11px;color:#a3a3a3;text-transform:uppercase;letter-spacing:.08em;font-weight:600;margin-bottom:8px}
.field{margin-bottom:16px}
input{width:100%;padding:13px 14px;border-radius:12px;border:1px solid #262626;background:#0a0a0a;color:#fff;font-size:15px;font-family:inherit;outline:none}
input:focus{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.15)}
button{width:100%;padding:14px;border:0;border-radius:12px;margin-top:8px;font-size:15px;font-weight:700;font-family:inherit;cursor:pointer;color:#fff;background:linear-gradient(180deg,#f87171,#dc2626 50%,#991b1b);box-shadow:0 10px 28px rgba(220,38,38,.4)}
.ok{background:rgba(6,78,59,.4);border:1px solid #065f46;color:#6ee7b7;padding:12px;border-radius:12px;margin-bottom:16px;font-size:13px}
.err{background:rgba(127,29,29,.4);border:1px solid #991b1b;color:#fecaca;padding:12px;border-radius:12px;margin-bottom:16px;font-size:13px}
.links{text-align:center;margin-top:22px;font-size:13px}
.links a{color:#737373;margin:0 10px;text-decoration:none}
</style></head><body>
<div class="top"><a href="/dashboard">← Dashboard</a><a href="/keys">All Keys</a></div>
<div class="wrap">
<div class="brand"><div class="logo">UP</div><h1>User + Password</h1><p class="sub">App login · 0 = unlimited</p></div>
<div class="card">'.$msg.'
<form method="POST" action="/make-user">
<input type="hidden" name="_token" value="'.$token.'">
<div class="field"><label>Username</label><input type="text" name="username" required placeholder="Username"></div>
<div class="field"><label>Password</label><input type="password" name="password" required placeholder="Password"></div>
<div class="field"><label>Max uses (0 = unlimited)</label><input type="number" name="max_uses" value="0" min="0"></div>
<button type="submit">Create User</button>
</form></div>
<div class="links"><a href="/dashboard">Dashboard</a><a href="/keys">Keys</a><a href="/keys/create">Generate</a></div>
</div></body></html>');
});

Route::middleware('auth')->post('/make-user', function (\Illuminate\Http\Request $request) {
    $user = trim((string) $request->input('username'));
    $pass = (string) $request->input('password');
    $max  = (int) $request->input('max_uses', 0);
    if ($user === '' || $pass === '') {
        return redirect('/make-user')->with('error', 'Username/password empty');
    }
    if (!\Illuminate\Support\Facades\Schema::hasTable('keys')) {
        return redirect('/make-user')->with('error', 'Keys table missing');
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
    $row->max_uses = $max;
    $row->used_count = 0;
    $row->save();
    return redirect('/make-user')->with('success', 'Created: '.$user);
});
