@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">📕 Daftar Buku</h1>
            <p class="text-slate-500 mt-1 text-sm">Kelola koleksi buku perpustakaan</p>
        </div>
        <a href="{{ route('books.create') }}" id="btn-add-book"
           class="bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md shadow-primary-200 hover:shadow-lg hover:shadow-primary-300 transition-all duration-200">
            + Tambah Buku
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <form method="GET" action="{{ route('books.index') }}" class="flex gap-3" id="search-form">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" id="search-input" placeholder="Cari judul, penulis, atau ISBN..."
                        value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm">
                </div>
                <button type="submit" id="btn-search" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm">Cari</button>
                @if (request('search'))
                    <a href="{{ route('books.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="books-table">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ISBN</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tersedia</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($books as $book)
                        <tr class="hover:bg-primary-50/30 transition-colors duration-150" id="book-row-{{ $book->id }}">
                            <td class="px-6 py-4 text-sm text-slate-900 font-semibold">{{ $book->title }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $book->author }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">{{ $book->isbn ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($book->category)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-50 text-primary-700">
                                        {{ $book->category }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 font-medium">{{ $book->stock }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $book->available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $book->available }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('books.show', $book) }}" class="text-primary-600 hover:text-primary-800 font-medium transition-colors duration-150" title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('books.edit', $book) }}" class="text-amber-500 hover:text-amber-700 font-medium transition-colors duration-150" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline" id="delete-book-{{ $book->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus buku &quot;{{ $book->title }}&quot;?')" class="text-red-400 hover:text-red-600 transition-colors duration-150" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-4xl mb-3">📚</span>
                                    <p class="text-slate-500 font-medium">Tidak ada buku ditemukan.</p>
                                    <a href="{{ route('books.create') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium mt-2">+ Tambah buku pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($books->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $books->links() }}
            </div>
        @endif
    </div>
@endsection
