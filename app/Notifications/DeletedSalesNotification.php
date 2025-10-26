<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Sale;
use Illumnate\Support\Facades\Auth;

class DeletedSalesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sale Deleted Notification')
            ->greeting('Hello Admin,')
            ->line("A sale has been deleted.")
            ->line("Sale ID: {$this->sale->id}")
            ->line("Customer: {$this->sale->customer_name}")
            ->line("Total Amount: {$this->sale->total}")
            ->line('Deleted by: ' . Auth::user()->name)
            ->action('View Sales', url('/sales'))
            ->line('Thank you.');
    }
}
