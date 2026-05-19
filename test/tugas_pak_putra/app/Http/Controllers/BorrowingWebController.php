<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BorrowingWebController extends Controller
{
    /**
     * Display borrowing transactions page (Web view).
     */
    public function index(Request $request): View
    {
        $availableBooks = Book::where('available', '>', 0)->orderBy('title')->get();

        /** @var User $user */
        $user = Auth::user();
        $myActiveBorrowings = $user->borrowings()
            ->where('status', 'borrowed')
            ->with('book:id,title,author')
            ->get();

        return view('borrowings.index', compact('availableBooks', 'myActiveBorrowings'));
    }

    /**
     * Store a new borrowing transaction (Web form).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'due_days' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $book = Book::findOrFail($validated['book_id']);

        // Check availability
        if (! $book->isAvailable()) {
            return redirect()->back()
                ->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }

        // Check if already borrowed
        $existingBorrow = Borrowing::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($existingBorrow) {
            return redirect()->back()
                ->with('error', 'Anda masih memiliki buku ini. Kembalikan terlebih dahulu.');
        }

        $borrowDate = now();
        $dueDate = $borrowDate->copy()->addDays((int) $validated['due_days']);

        Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'notes' => $validated['notes'] ?? null,
            'status' => 'borrowed',
        ]);

        $book->decrement('available');

        return redirect()->route('borrowings.history')
            ->with('success', "Buku '{$book->title}' berhasil dipinjam hingga ".$dueDate->format('d-m-Y'));
    }

    /**
     * Return a borrowed book (Web form).
     */
    public function return(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status === 'returned') {
            return redirect()->back()
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }

        $calculatedFine = $borrowing->calculateFine();
        $borrowing->markAsReturned($calculatedFine);

        $message = 'Buku berhasil dikembalikan.';
        if ($calculatedFine > 0) {
            $message .= ' Denda: Rp '.number_format($calculatedFine, 0, ',', '.');
        }

        return redirect()->route('borrowings.history')
            ->with('success', $message);
    }

    /**
     * Display user's borrowing history (Web view).
     */
    public function history(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $status = $request->string('status', '');

        $query = $user->borrowings()->with('book:id,title,author');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $borrowings = $query->latest('borrow_date')->paginate(15);

        return view('borrowings.history', compact('borrowings'));
    }

    /**
     * Display home dashboard (Web view).
     */
    public function dashboard(): View
    {
        $totalBooks = Book::count();
        $borrowedCount = Borrowing::where('status', 'borrowed')->count();
        $overdueCount = Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        /** @var User $user */
        $user = Auth::user();
        $myBorrowings = $user->borrowings()->where('status', 'borrowed')->count();
        $latestBooks = Book::latest()->take(5)->get();

        return view('home', compact(
            'totalBooks',
            'borrowedCount',
            'overdueCount',
            'myBorrowings',
            'latestBooks'
        ));
    }
}
