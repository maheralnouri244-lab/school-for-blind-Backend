<?php

namespace App\Providers;

use App\Models\Exam;
use App\Models\PastExam;
use App\Models\Room;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }
        Paginator::useBootstrapFive();
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        View::composer('partials.sidebar', function ($view) {
            $activeCallsCount = Room::where('status', 'active')->count();
            $view->with('activeCallsCount', $activeCallsCount);
        });
        
        Relation::morphMap([
            'student'   => \App\Models\Student::class,
            'teacher'   => \App\Models\Teacher::class,
            'caregiver' => \App\Models\Caregiver::class,
            'PastExam' => \App\Models\PastExam::class,
        'Exam'=> \App\Models\Exam::class,
        'lesson'=> \App\Models\Lesson::class,
        ]);
    }
}
