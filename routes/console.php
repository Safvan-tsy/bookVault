<?php

use App\Models\BorrowRecord;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Notifications\BookOverdueNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $overdueBorrows = BorrowRecord::with(['book', 'user'])
        ->whereNull('returned_at')
        ->where('due_date', '<', now())
        ->get();

    foreach ($overdueBorrows as $borrow) {
        $daysOverdue = $borrow->getDaysOverdue();
        $borrow->user->notify(new BookOverdueNotification($borrow->book, $daysOverdue));
    }
})->everySixHours();