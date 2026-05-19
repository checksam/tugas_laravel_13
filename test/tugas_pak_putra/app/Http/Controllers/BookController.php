<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display all books (Web view).
     */
    public function index(Request $request): View
    {
        $search = $request->string('search', '');
        $query = Book::query();

        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
        }

        $books = $query->orderBy('title')->paginate(15);

        return view('books.index', compact('books'));
    }

    /**
     * Show form for creating new book (Web view).
     */
    public function create(): View
    {
        return view('books.form');
    }

    /**
     * Store a newly created book (Web form).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:'.date('Y'),
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        $validated['available'] = $validated['stock'];
        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Display a specific book (Web view).
     */
    public function show(Book $book): View
    {
        $book->load('borrowings.user');

        return view('books.show', compact('book'));
    }

    /**
     * Show form for editing a book (Web view).
     */
    public function edit(Book $book): View
    {
        return view('books.form', compact('book'));
    }

    /**
     * Update a book (Web form).
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,'.$book->id,
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:'.date('Y'),
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['stock'])) {
            $borrowed = $book->getBorrowedCount();
            $validated['available'] = max(0, $validated['stock'] - $borrowed);
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book): RedirectResponse
    {
        $title = $book->title;
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', "Buku '{$title}' berhasil dihapus.");
    }
}
