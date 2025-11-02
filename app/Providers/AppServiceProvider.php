<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Book;
use App\Models\Member;
use App\Models\Teacher;
use App\Observers\BookObserver;
use App\Observers\MemberObserver;
use App\Observers\TeacherObserver;

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
        // Register observers for auto-sync to Google Sheets
        Book::observe(BookObserver::class);
        Member::observe(MemberObserver::class);
        Teacher::observe(TeacherObserver::class);
    }
}
