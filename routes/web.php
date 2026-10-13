<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DisplayPublicController;
use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

// ── Redirect root ke dashboard ──────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('contents.index')
        : redirect()->route('login');
});

// ── Public display routes (akses tanpa auth — untuk layar TV) ───────────────
Route::get('/display/{token}', [DisplayPublicController::class, 'show'])->name('display.show');
Route::get('/api/display/{token}/active', [DisplayPublicController::class, 'activeContent'])->name('display.active');
Route::get('/stream/media/{path}', [DisplayPublicController::class, 'streamMedia'])->where('path', '.*')->name('media.stream');

// ── Auth protected routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contents (Request Videowall)
    Route::resource('contents', ContentController::class)->except(['show']);
    Route::post('contents/{content}/expire', [ContentController::class, 'expire'])->name('contents.expire');

    // Displays (Manajemen TV/layar)
    Route::resource('displays', DisplayController::class);
    Route::post('displays/{display}/regenerate-token', [DisplayController::class, 'regenerateToken'])
        ->name('displays.regenerate-token');

    // Assets (Logo, Video BG, Image BG)
    Route::resource('assets', AssetController::class)->except(['show', 'edit', 'update']);
    Route::post('assets/ajax-upload', [AssetController::class, 'ajaxUpload'])->name('assets.ajax-upload');
});

require __DIR__ . '/auth.php';
