<?php

use Illuminate\Support\Facades\Route;
use Laragear\WebAuthn\Http\Routes as WebAuthnRoutes;

// Passkey login
if (config('auth.method') === 'passkey') {
    Route::get('/login', fn () => view('auth.passkey'))->name('login');
    WebAuthnRoutes::register()->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
}

// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});
