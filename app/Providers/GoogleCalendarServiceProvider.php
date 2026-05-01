<?php

namespace App\Providers;

use App\Listeners\SyncBookingToGoogleCalendar;
use App\Events\BookingCreated;
use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\GoogleCalendarService::class, function ($app) {
            return new \App\Services\GoogleCalendarService();
        });
    }

    public function boot(): void
    {
        //
    }
}
