<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookBorrowedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $book;
    protected $dueDate;

    public function __construct(Book $book, $dueDate)
    {
        $this->book = $book;
        $this->dueDate = $dueDate;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You borrowed a book from BookVault')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have successfully borrowed the book:')
            ->line('📖 ' . $this->book->title . ' by ' . $this->book->author)
            ->line('Category: ' . $this->book->category->name)
            ->line('Due Date: ' . $this->dueDate->format('d M Y'))
            ->action('View Your Borrows', url('/borrows'))
            ->line('Please return the book on time to avoid overdue reminders. 😊');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'book_id' => $this->book->id,
            'title' => $this->book->title,
            'due_date' => $this->dueDate,
        ];
    }
}
