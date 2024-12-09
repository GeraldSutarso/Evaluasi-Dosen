<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();
        ini_set('memory_limit', '1024M'); // Adjust the memory limit as needed
    	ini_set('max_execution_time', '1200'); // Adjust the execution time as needed
        //
    }
}
