<?php

namespace App\Listeners;

use App\Events\AbsenceCreatedEvent;
use App\Notifications\AbsenceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAbsenceCreatedNotification implements ShouldQueue
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
    public function handle(AbsenceCreatedEvent $event): void
    {
        // Notify the employee (person) that an absence has been created
        $event->absence->person->user?->notify(
            new AbsenceCreated($event->absence)
        );
    }
}
