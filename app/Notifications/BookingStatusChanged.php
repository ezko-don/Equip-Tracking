<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChanged extends Notification implements ShouldQueue
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
        $status = ucfirst($this->booking->status);
        $equipment = $this->booking->equipment->name;
        $event = $this->booking->event_name;
        $date = $this->booking->start_time->format('M d, Y h:i A');

        return (new MailMessage)
            ->subject("Booking Status Changed - {$equipment}")
            ->line("Your booking for {$equipment} has been {$status}.")
            ->line("Event: {$event}")
            ->line("Date: {$date}")
            ->action('View Booking', url("/bookings/{$this->booking->id}"))
            ->line('Thank you for using our equipment management system!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'equipment_name' => $this->booking->equipment->name,
            'event_name' => $this->booking->event_name,
            'status' => $this->booking->status,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
        ];
    }
} 