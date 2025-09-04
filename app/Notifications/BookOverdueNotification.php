<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $book;
    protected $daysOverdue;

    public function __construct(Book $book, $daysOverdue)
    {
        $this->book = $book;
        $this->daysOverdue = $daysOverdue;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: Book Overdue - ' . $this->book->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Our records show that the following book is overdue:')
            ->line('📖 ' . $this->book->title . ' by ' . $this->book->author)
            ->line('Category: ' . $this->book->category->name)
            ->line($this->daysOverdue == 0 ? 'It is due today.' : 'It is overdue by ' . $this->daysOverdue . ' day' . ($this->daysOverdue > 1 ? 's' : '') . '.')
            ->action('View Your Borrows', url('/borrows'))
            ->line('Please return the book as soon as possible to avoid further reminders.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'book_id' => $this->book->id,
            'title' => $this->book->title,
            'days_overdue' => $this->daysOverdue,
        ];
    }
}
