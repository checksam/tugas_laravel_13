@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $book->title }}</h1>
            <p class="text-slate-500 mt-1">oleh {{ $book->author }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('books.edit', $book) }}" id="btn-edit-book"
               class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm shadow-sm">
                ✏️ Edit
            </a>
            <a href="{{ route('books.index') }}" id="btn-back"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Penulis</h3>
                        <p class="text-lg font-semibold text-slate-900">{{ $book->author }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">ISBN</h3>
                        <p class="text-lg font-semibold text-slate-900 font-mono">{{ $book->isbn ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Penerbit</h3>
                        <p class="text-lg font-semibold text-slate-900">{{ $book->publisher ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tahun Terbit</h3>
                        <p class="text-lg font-semibold text-slate-900">{{ $book->publication_year ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Kategori</h3>
                    @if ($book->category)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-primary-50 text-primary-700">
                            {{ $book->category }}
                        </span>
                    @else
                        <p class="text-slate-500">-</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $book->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-5">Informasi Stok</h3>
                <div class="space-y-5">
                    <div class="flex justify-between items-center">
                        <p class="text-slate-500 text-sm">Total Stok</p>
                        <p class="text-2xl font-bold text-primary-600">{{ $book->stock }}</p>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div class="flex justify-between items-center">
                        <p class="text-slate-500 text-sm">Tersedia</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ $book->available }}</p>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div class="flex justify-between items-center">
                        <p class="text-slate-500 text-sm">Dipinjam</p>
                        <p class="text-2xl font-bold text-amber-500">{{ $book->getBorrowedCount() }}</p>
                    </div>
                    @if ($book->price)
                        <div class="h-px bg-slate-100"></div>
                        <div class="flex justify-between items-center">
                            <p class="text-slate-500 text-sm">Harga</p>
                            <p class="text-lg font-bold text-slate-900">
                                Rp {{ number_format($book->price, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Status</h3>
                <div>
                    @if ($book->isAvailable())
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ✓ Tersedia untuk dipinjam
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-700 border border-red-200">
                            ✗ Tidak tersedia
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($book->borrowings->count() > 0)
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-900">📋 Riwayat Peminjaman (10 Terbaru)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Peminjam</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Kembali</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($book->borrowings->sortByDesc('borrow_date')->take(10) as $borrowing)
                            <tr class="hover:bg-primary-50/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ $borrowing->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $borrowing->borrow_date->format('d-m-Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $borrowing->return_date?->format('d-m-Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $borrowing->status === 'returned' ? 'bg-emerald-50 text-emerald-700' : ($borrowing->isOverdue() ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ $borrowing->status === 'returned' ? '✓ Dikembalikan' : ($borrowing->isOverdue() ? '⚠ Terlambat' : '📤 Dipinjam') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
