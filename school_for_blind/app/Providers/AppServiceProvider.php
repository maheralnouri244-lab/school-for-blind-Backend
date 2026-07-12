<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }
        Paginator::useBootstrapFive();
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        // Relation::morphMap([
        //     'student'   => \App\Models\Student::class,
        //     'teacher'   => \App\Models\Teacher::class,
        //     'caregiver' => \App\Models\Caregiver::class,
        // ]);
    }
}
