<?php

namespace App\Providers;

use App\Events\SppdApproved;
use App\Events\SppdRejected;
use App\Listeners\SendSppdNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SppdApproved::class => [SendSppdNotification::class],
        SppdRejected::class => [SendSppdNotification::class],
    ];
}

