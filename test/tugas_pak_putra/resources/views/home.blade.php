@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-slate-500 mt-1 text-sm">Dashboard Sistem Manajemen Perpustakaan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Buku</h3>
                <span class="text-2xl">📚</span>
            </div>
            <p class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">{{ $totalBooks ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Sedang Dipinjam</h3>
                <span class="text-2xl">📤</span>
            </div>
            <p class="text-3xl font-bold text-amber-500">{{ $borrowedCount ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Terlambat</h3>
                <span class="text-2xl">⚠️</span>
            </div>
            <p class="text-3xl font-bold text-red-500">{{ $overdueCount ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Peminjaman Saya</h3>
                <span class="text-2xl">📖</span>
            </div>
            <p class="text-3xl font-bold text-emerald-500">{{ $myBorrowings ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-5">🚀 Navigasi Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('books.index') }}" id="nav-books"
                   class="flex items-center justify-between p-4 bg-primary-50/50 hover:bg-primary-100/60 rounded-xl text-primary-700 font-medium transition-all duration-200 group">
                    <span class="text-sm">📕 Kelola Buku</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('borrowings.index') }}" id="nav-borrowings"
                   class="flex items-center justify-between p-4 bg-emerald-50/50 hover:bg-emerald-100/60 rounded-xl text-emerald-700 font-medium transition-all duration-200 group">
                    <span class="text-sm">📤 Transaksi Peminjaman</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('borrowings.history') }}" id="nav-history"
                   class="flex items-center justify-between p-4 bg-violet-50/50 hover:bg-violet-100/60 rounded-xl text-violet-700 font-medium transition-all duration-200 group">
                    <span class="text-sm">📋 Riwayat Peminjaman</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-5">📚 Buku Terbaru</h2>
            <div class="space-y-3">
                @forelse ($latestBooks ?? [] as $book)
                    <a href="{{ route('books.show', $book) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors duration-150 group">
                        <div>
                            <p class="font-semibold text-slate-900 text-sm group-hover:text-primary-600 transition-colors duration-150">{{ $book->title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $book->author }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $book->available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                            {{ $book->available }}/{{ $book->stock }}
                        </span>
                    </a>
                @empty
                    <div class="text-center py-6">
                        <p class="text-slate-400 text-sm">Belum ada buku ditambahkan.</p>
                        <a href="{{ route('books.create') }}" class="text-primary-600 text-sm font-medium mt-1 inline-block">+ Tambah buku pertama</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
