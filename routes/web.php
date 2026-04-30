<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegistrationForm'])->name('auth.register.show');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register');
    Route::get('/verify', [\App\Http\Controllers\AuthController::class, 'showVerifyForm'])->name('auth.verify.show');
    Route::post('/verify', [\App\Http\Controllers\AuthController::class, 'verifyOtp'])->name('auth.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile/setup', [\App\Http\Controllers\ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/setup', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/theme', [\App\Http\Controllers\SettingsController::class, 'theme'])->name('settings.theme');
    Route::post('/settings/theme', [\App\Http\Controllers\SettingsController::class, 'updateTheme'])->name('settings.theme.update');

    Route::middleware([\App\Http\Middleware\EnsureProfileComplete::class])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        // Auction Management
        Route::get('/auctions/create', [\App\Http\Controllers\AuctionController::class, 'create'])->name('auctions.create');
        Route::post('/auctions', [\App\Http\Controllers\AuctionController::class, 'store'])->name('auctions.store');
        
        Route::get('/auctions/join', [\App\Http\Controllers\JoinAuctionController::class, 'create'])->name('auctions.join');
        Route::post('/auctions/join', [\App\Http\Controllers\JoinAuctionController::class, 'store'])->name('auctions.join.store');

        Route::get('/auctions/joined', [\App\Http\Controllers\AuctionHistoryController::class, 'joined'])->name('auctions.joined');
        Route::get('/auctions/history', [\App\Http\Controllers\AuctionHistoryController::class, 'index'])->name('auctions.history');
        Route::get('/auctions/{auction}', [\App\Http\Controllers\AuctionHistoryController::class, 'show'])->name('auctions.show');
        Route::get('/auctions/{auction}/statistics', [\App\Http\Controllers\AuctionHistoryController::class, 'statistics'])->name('auctions.statistics');

        // Player Management
        Route::get('/players', [\App\Http\Controllers\PlayerController::class, 'index'])->name('players.index');
        Route::get('/players/create', [\App\Http\Controllers\PlayerController::class, 'create'])->name('players.create');
        Route::post('/players', [\App\Http\Controllers\PlayerController::class, 'store'])->name('players.store');
        Route::get('/players/{player}', [\App\Http\Controllers\PlayerController::class, 'show'])->name('players.show');
        Route::get('/players/{player}/edit', [\App\Http\Controllers\PlayerController::class, 'edit'])->name('players.edit');
        Route::put('/players/{player}', [\App\Http\Controllers\PlayerController::class, 'update'])->name('players.update');
        Route::delete('/players/{player}', [\App\Http\Controllers\PlayerController::class, 'destroy'])->name('players.destroy');
        Route::get('/players/search', [\App\Http\Controllers\PlayerController::class, 'search'])->name('players.search');

        // Auction Player Management
        Route::get('/auctions/{auction}/players', [\App\Http\Controllers\PlayerController::class, 'showAuctionPlayers'])->name('auctions.players');
        Route::post('/auctions/{auction}/players', [\App\Http\Controllers\PlayerController::class, 'addToAuction'])->name('auctions.players.store');
        Route::delete('/auctions/{auction}/players/{player}', [\App\Http\Controllers\PlayerController::class, 'removeFromAuction'])->name('auctions.players.remove');

        // Team Management
        Route::get('/teams/joined', [\App\Http\Controllers\JoinedTeamsController::class, 'index'])->name('teams.joined');
        Route::get('/teams/{team}', [\App\Http\Controllers\JoinedTeamsController::class, 'show'])->name('teams.show');
        Route::get('/auctions/{auction}/teams', [\App\Http\Controllers\JoinedTeamsController::class, 'auctionTeams'])->name('teams.auction');

        // Auction Pool Management
        Route::get('/auctions/{auction}/pool', [\App\Http\Controllers\PlayerPoolController::class, 'index'])->name('auctions.pool');
        Route::post('/auctions/{auction}/pool', [\App\Http\Controllers\PlayerPoolController::class, 'store'])->name('auctions.pool.store');
        Route::delete('/auctions/{auction}/pool/{poolPlayer}', [\App\Http\Controllers\PlayerPoolController::class, 'remove'])->name('auctions.pool.remove');

        // Live Auction
        Route::get('/auctions/{auction}/live', [\App\Http\Controllers\LiveAuctionController::class, 'index'])->name('auctions.live');
        Route::post('/auctions/{auction}/start', [\App\Http\Controllers\LiveAuctionController::class, 'start'])->name('auctions.start');
        Route::post('/auctions/{auction}/spin', [\App\Http\Controllers\LiveAuctionController::class, 'spin'])->name('auctions.spin');
        Route::post('/auctions/{auction}/bid', [\App\Http\Controllers\LiveAuctionController::class, 'bid'])->name('auctions.bid');
        Route::post('/auctions/{auction}/sell', [\App\Http\Controllers\LiveAuctionController::class, 'sell'])->name('auctions.sell');
        Route::post('/auctions/{auction}/unsold', [\App\Http\Controllers\LiveAuctionController::class, 'unsold'])->name('auctions.unsold');
    });
});
