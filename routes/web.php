<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\AdminMapelController;
use App\Http\Controllers\Admin\AdminSoalController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }

    return view('landing');
})->name('home');

// ===== AUTH =====
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisterController::class, 'show'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/masuk', [LoginController::class, 'show'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/keluar', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ===== USER (wajib login) =====
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mapel/{mapel}', [DashboardController::class, 'mapel'])->name('mapel.show');

    Route::get('/game/{game}', [GameController::class, 'show'])->name('games.show');
    Route::post('/game/{game}/submit', [GameController::class, 'submit'])->name('games.submit');
    Route::get('/game/{game}/hasil', [GameController::class, 'result'])->name('games.result');

    Route::get('/langganan', [SubscriptionController::class, 'index'])->name('subscription');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/langganan/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/langganan/bayar/{subscription}', [SubscriptionController::class, 'pay'])->name('subscription.pay');
    Route::post('/langganan/callback/{subscription}', [SubscriptionController::class, 'callback'])->name('subscription.callback');
    Route::get('/langganan/hasil/{subscription}', [SubscriptionController::class, 'result'])->name('subscription.result');
});

// ===== ADMIN =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/langganan', [AdminUserController::class, 'subscription'])->name('users.subscription');
    Route::post('/users/{user}/batal-langganan', [AdminUserController::class, 'cancelSubscription'])->name('users.cancel-subscription');

    Route::get('/mapels', [AdminMapelController::class, 'index'])->name('mapels.index');
    Route::post('/mapels', [AdminMapelController::class, 'store'])->name('mapels.store');
    Route::put('/mapels/{mapel}', [AdminMapelController::class, 'update'])->name('mapels.update');
    Route::delete('/mapels/{mapel}', [AdminMapelController::class, 'destroy'])->name('mapels.destroy');

    Route::get('/games', [AdminGameController::class, 'index'])->name('games.index');
    Route::get('/games/create', [AdminGameController::class, 'create'])->name('games.create');
    Route::post('/games', [AdminGameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', [AdminGameController::class, 'edit'])->name('games.edit');
    Route::put('/games/{game}', [AdminGameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [AdminGameController::class, 'destroy'])->name('games.destroy');

    Route::get('/games/{game}/soals', [AdminSoalController::class, 'index'])->name('soals.index');
    Route::post('/games/{game}/soals', [AdminSoalController::class, 'store'])->name('soals.store');
    Route::put('/games/{game}/soals/{soal}', [AdminSoalController::class, 'update'])->name('soals.update');
    Route::delete('/games/{game}/soals/{soal}', [AdminSoalController::class, 'destroy'])->name('soals.destroy');
});
