<?php

namespace App\Notifications;

use App\Filament\App\Resources\MyAbsenceResource\Pages\ViewMyAbsence;
use App\Models\Absence;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private Absence $absence;

    /**
     * Create a new notification instance.
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('An absence is registered.'))
            ->line(__('See more details by clicking on the button below.'))
            ->action(__('View details'), ViewMyAbsence::getUrl(['record' => $this->absence]));
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title(__("An absence is registered"))
            ->body(fn (): string => $this->absence->absenceType->name)
            ->actions([
                Action::make('view')
                    ->label(__("View"))
                    ->url(ViewMyAbsence::getUrl(['record' => $this->absence]))
                    ->markAsRead()
            ])
            ->getDatabaseMessage();
    }
}
