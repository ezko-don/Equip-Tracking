<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Request Submitted - ' . $this->booking->equipment->name)
            ->line('Your booking request has been submitted successfully.')
            ->line('Equipment: ' . $this->booking->equipment->name)
            ->line('Event: ' . $this->booking->event_name)
            ->line('Date: ' . $this->booking->start_time->format('M d, Y h:i A'))
            ->line('Location: ' . $this->booking->location)
            ->line('Status: Pending approval')
            ->action('View Booking', route('bookings.show', $this->booking))
            ->line('You will be notified when an admin reviews your booking request.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'equipment_name' => $this->booking->equipment->name,
            'event_name' => $this->booking->event_name,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'location' => $this->booking->location,
            'status' => 'pending'
        ];
    }
} 