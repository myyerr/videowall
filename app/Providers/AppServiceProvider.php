<?php

namespace App\Providers;

use App\Models\Display;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share active displays to all authenticated views (for topbar preview links)
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('displays', Display::where('status', 'active')->get());
            }
        });
    }
}
