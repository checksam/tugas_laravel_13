<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    use JsonResponseTrait;

    /**
     * Get all borrowing records (admin view).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $status = $request->string('status', '');
        $userId = $request->integer('user_id');

        $query = Borrowing::with(['user:id,name,email', 'book:id,title,author']);

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($userId > 0) {
            $query->where('user_id', $userId);
        }

        $borrowings = $query->latest('borrow_date')->paginate($perPage);

        return $this->paginatedResponse($borrowings, 'Daftar peminjaman berhasil dimuat.');
    }

    /**
     * Borrow a book.
     */
    public function borrow(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'due_days' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $book = Book::find($validated['book_id']);

        // Check if book is available
        if (! $book->isAvailable()) {
            return $this->conflictResponse('Buku tidak tersedia.');
        }

        // Check if user already borrowed this book and hasn't returned it
        $existingBorrow = Borrowing::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($existingBorrow) {
            return $this->conflictResponse('Anda masih memiliki buku ini. Kembalikan terlebih dahulu.');
        }

        $borrowDate = now();
        $dueDate = $borrowDate->copy()->addDays((int) $validated['due_days']);

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'notes' => $validated['notes'] ?? null,
            'status' => 'borrowed',
        ]);

        // Decrease available count
        $book->decrement('available');

        return $this->createdResponse(
            data: $borrowing->load(['user:id,name,email', 'book:id,title,author']),
            message: 'Buku berhasil dipinjam.',
        );
    }

    /**
     * Return a borrowed book.
     */
    public function return(Request $request, Borrowing $borrowing): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Check if book is already returned
        if ($borrowing->status === 'returned') {
            return $this->conflictResponse('Buku sudah dikembalikan sebelumnya.');
        }

        $calculatedFine = $borrowing->calculateFine();
        $borrowing->update(['notes' => $validated['notes'] ?? null]);

        $borrowing->markAsReturned($calculatedFine);

        return $this->successResponse(
            data: [
                'borrowing' => $borrowing->fresh()->load(['user:id,name,email', 'book:id,title,author']),
                'fine' => $calculatedFine,
            ],
            message: $calculatedFine > 0
                ? 'Buku berhasil dikembalikan. Denda: Rp '.number_format($calculatedFine, 0, ',', '.')
                : 'Buku berhasil dikembalikan.',
        );
    }

    /**
     * Get borrowing history for current user.
     */
    public function myHistory(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $perPage = $request->integer('per_page', 15);
        $status = $request->string('status', '');

        $query = $user->borrowings()->with('book:id,title,author');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $borrowings = $query->latest('borrow_date')->paginate($perPage);

        return $this->paginatedResponse($borrowings, 'Riwayat peminjaman berhasil dimuat.');
    }

    /**
     * Get user's currently borrowed books.
     */
    public function myActiveBorrowings(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $borrowings = $user->borrowings()
            ->where('status', 'borrowed')
            ->with('book:id,title,author')
            ->get()
            ->map(function (Borrowing $borrowing) {
                return [
                    'id' => $borrowing->id,
                    'book' => $borrowing->book,
                    'borrow_date' => $borrowing->borrow_date,
                    'due_date' => $borrowing->due_date,
                    'is_overdue' => $borrowing->isOverdue(),
                    'days_left' => now()->diffInDays($borrowing->due_date, false),
                ];
            });

        return $this->successResponse(
            data: $borrowings,
            message: 'Daftar peminjaman aktif berhasil dimuat.',
        );
    }

    /**
     * Get borrowing history for a specific user (admin only).
     */
    public function userHistory(Request $request, int $userId): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $status = $request->string('status', '');

        $query = Borrowing::where('user_id', $userId)
            ->with(['book:id,title,author', 'user:id,name,email']);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $borrowings = $query->latest('borrow_date')->paginate($perPage);

        return $this->paginatedResponse($borrowings, 'Riwayat peminjaman user berhasil dimuat.');
    }

    /**
     * Get overdue borrowings.
     */
    public function getOverdue(): JsonResponse
    {
        $overdueBorrowings = Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->with(['user:id,name,email', 'book:id,title,author'])
            ->orderBy('due_date')
            ->paginate(15);

        return $this->paginatedResponse($overdueBorrowings, 'Daftar peminjaman terlambat berhasil dimuat.');
    }

    /**
     * Get statistics about borrowings.
     */
    public function getStatistics(): JsonResponse
    {
        $totalBorrowed = Borrowing::where('status', 'borrowed')->count();
        $totalReturned = Borrowing::where('status', 'returned')->count();
        $totalOverdue = Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        $totalFineCollected = Borrowing::where('status', 'returned')
            ->sum('fine');

        return $this->successResponse(
            data: [
                'total_borrowed' => $totalBorrowed,
                'total_returned' => $totalReturned,
                'total_overdue' => $totalOverdue,
                'total_fine_collected' => (int) $totalFineCollected,
            ],
            message: 'Statistik peminjaman berhasil dimuat.',
        );
    }
}
