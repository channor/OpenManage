<?php

namespace App\Listeners;

use App\Events\AbsenceStatusUpdatedEvent;
use App\Notifications\AbsenceStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAbsenceStatusUpdatedNotification implements ShouldQueue
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
    public function handle(AbsenceStatusUpdatedEvent $event): void
    {
        // Notify the employee (person) that an absence has been created
        $event->absence->person->user?->notify(
            new AbsenceStatusUpdated($event->absence)
        );
    }
}
