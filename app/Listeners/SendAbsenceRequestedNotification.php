<?php

namespace App\Listeners;

use App\Events\AbsenceRequestedEvent;
use App\Notifications\AbsenceRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAbsenceRequestedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AbsenceRequestedEvent $event): void
    {
        $absence = $event->absence;

        // 1. Retrieve the managers for this absence (a collection of User models).
        $managers = $absence->managers();

        $managers->each(fn($manager) => $manager->notify(new AbsenceRequested($absence)));
    }
}
