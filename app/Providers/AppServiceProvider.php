<?php

namespace App\Providers;

use App\Models\PenerimaanBarang;
use App\Observers\PenerimaanBarangObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PenerimaanBarang::observe(PenerimaanBarangObserver::class);
    }
}
