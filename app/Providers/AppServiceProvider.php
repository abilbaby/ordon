<?php

namespace App\Providers;

use App\Models\AllocationMatch;
use App\Models\Donor;
use App\Models\Recipient;
use App\Models\Transplant;
use App\Observers\StatusHistoryObserver;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        Donor::observe(StatusHistoryObserver::class);
        Recipient::observe(StatusHistoryObserver::class);
        AllocationMatch::observe(StatusHistoryObserver::class);
        Transplant::observe(StatusHistoryObserver::class);
    }
}
