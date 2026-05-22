<?php

namespace App\Jobs;

use App\Models\Borrowing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Process borrowing notifications (overdue reminders, return confirmations).
 *
 * Dispatched when a borrow or return action occurs.
 */
class ProcessBorrowingNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Maximum retry attempts with exponential backoff.
     */
    public int $tries = 3;

    /**
     * Exponential backoff intervals (seconds).
     *
     * @var list<int>
     */
    public array $backoff = [1, 5, 10];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Borrowing $borrowing,
        public string $type = 'borrowed',
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $borrowing = $this->borrowing->load(['user', 'book']);

        match ($this->type) {
            'borrowed' => $this->handleBorrowed($borrowing),
            'returned' => $this->handleReturned($borrowing),
            'overdue_reminder' => $this->handleOverdueReminder($borrowing),
            default => Log::warning('Unknown borrowing notification type', [
                'type' => $this->type,
                'borrowing_id' => $borrowing->id,
            ]),
        };
    }

    /**
     * Handle borrow confirmation notification.
     */
    private function handleBorrowed(Borrowing $borrowing): void
    {
        Log::info('Borrowing notification sent', [
            'type' => 'borrowed',
            'user' => $borrowing->user->name,
            'book' => $borrowing->book->title,
            'due_date' => $borrowing->due_date->format('d-m-Y'),
        ]);
    }

    /**
     * Handle return confirmation notification.
     */
    private function handleReturned(Borrowing $borrowing): void
    {
        Log::info('Return notification sent', [
            'type' => 'returned',
            'user' => $borrowing->user->name,
            'book' => $borrowing->book->title,
            'fine' => $borrowing->fine,
        ]);
    }

    /**
     * Handle overdue reminder notification.
     */
    private function handleOverdueReminder(Borrowing $borrowing): void
    {
        if (! $borrowing->isOverdue()) {
            return;
        }

        $daysOverdue = (int) $borrowing->due_date->diffInDays(now());

        Log::info('Overdue reminder sent', [
            'type' => 'overdue_reminder',
            'user' => $borrowing->user->name,
            'book' => $borrowing->book->title,
            'days_overdue' => $daysOverdue,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Borrowing notification failed', [
            'borrowing_id' => $this->borrowing->id,
            'type' => $this->type,
            'error' => $exception?->getMessage(),
        ]);
    }
}
