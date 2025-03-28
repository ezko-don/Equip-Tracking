<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class NewBookingRequest extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Equipment Booking Request - ' . $this->booking->equipment->name)
            ->line('A new booking request has been submitted.')
            ->line('Equipment: ' . $this->booking->equipment->name)
            ->line('User: ' . $this->booking->user->name)
            ->line('Event: ' . $this->booking->event_name)
            ->line('Date: ' . $this->booking->start_time->format('M d, Y h:i A') . ' to ' . $this->booking->end_time->format('M d, Y h:i A'))
            ->line('Location: ' . $this->booking->location)
            ->line('Purpose: ' . $this->booking->purpose)
            ->action('Review Booking', route('admin.bookings.show', $this->booking))
            ->line('Please review this request at your earliest convenience.');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'user_name' => $this->booking->user->name,
            'equipment_name' => $this->booking->equipment->name,
            'event_name' => $this->booking->event_name,
        ];
    }
} 