<?php

namespace Modules\CHFreeFlight\Providers;

use App\Events\CronNightly;
use Modules\CHFreeFlight\Listeners\DeleteFlights;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        CronNightly::class => [DeleteFlights::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
