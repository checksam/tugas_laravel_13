@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">📋 Riwayat Peminjaman</h1>
        <p class="text-slate-500 mt-1 text-sm">Semua transaksi peminjaman dan pengembalian buku Anda</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 mb-8">
        <form method="GET" action="{{ route('borrowings.history') }}" class="flex flex-wrap gap-3" id="filter-form">
            <select name="status" id="filter-status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200">
                <option value="">-- Semua Status --</option>
                <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>📤 Sedang Dipinjam</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>✓ Sudah Dikembalikan</option>
            </select>
            <button type="submit" id="btn-filter" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm">Filter</button>
            @if (request('status'))
                <a href="{{ route('borrowings.history') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-medium transition-colors duration-200 text-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($borrowings as $borrowing)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 border-l-4 {{ $borrowing->status === 'returned' ? 'border-l-emerald-500' : ($borrowing->isOverdue() ? 'border-l-red-500' : 'border-l-primary-500') }} hover:shadow-md transition-shadow duration-200" id="borrowing-{{ $borrowing->id }}">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-3 mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $borrowing->book->title }}</h3>
                        <p class="text-slate-500 text-sm">{{ $borrowing->book->author }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 {{ $borrowing->status === 'returned' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($borrowing->isOverdue() ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                        {{ $borrowing->status === 'returned' ? '✓ Dikembalikan' : ($borrowing->isOverdue() ? '⚠ Terlambat' : '📤 Sedang Dipinjam') }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                    <div class="bg-slate-50/60 rounded-xl p-3">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider mb-1">Tanggal Pinjam</p>
                        <p class="font-semibold text-slate-900 text-sm">{{ $borrowing->borrow_date->format('d-m-Y') }}</p>
                    </div>
                    <div class="bg-slate-50/60 rounded-xl p-3">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider mb-1">Batas Kembali</p>
                        <p class="font-semibold text-sm {{ $borrowing->isOverdue() ? 'text-red-600' : 'text-slate-900' }}">
                            {{ $borrowing->due_date->format('d-m-Y') }}
                        </p>
                    </div>
                    <div class="bg-slate-50/60 rounded-xl p-3">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider mb-1">Tanggal Kembali</p>
                        <p class="font-semibold text-slate-900 text-sm">{{ $borrowing->return_date?->format('d-m-Y') ?? '-' }}</p>
                    </div>
                    <div class="bg-slate-50/60 rounded-xl p-3">
                        <p class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider mb-1">Denda</p>
                        <p class="font-semibold text-sm {{ $borrowing->fine > 0 ? 'text-red-600' : 'text-slate-900' }}">
                            Rp {{ number_format($borrowing->fine, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if ($borrowing->notes)
                    <div class="mb-5 p-3 bg-slate-50/60 rounded-xl border border-slate-100">
                        <p class="text-sm text-slate-600"><span class="font-semibold text-slate-700">Catatan:</span> {{ $borrowing->notes }}</p>
                    </div>
                @endif

                @if ($borrowing->status === 'borrowed' && !$borrowing->return_date)
                    <div>
                        <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" id="return-history-{{ $borrowing->id }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Kembalikan buku &quot;{{ $borrowing->book->title }}&quot;?')"
                                class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white px-5 py-3 rounded-xl font-semibold shadow-md shadow-emerald-200 hover:shadow-lg transition-all duration-200 text-sm">
                                ✓ Kembalikan Buku
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-primary-50/50 border border-primary-100 rounded-2xl p-12 text-center">
                <span class="text-5xl mb-4 block">📋</span>
                <p class="text-primary-700 text-lg font-semibold">Tidak ada riwayat peminjaman.</p>
                <p class="text-primary-500 text-sm mt-2">Mulai pinjam buku di halaman <a href="{{ route('borrowings.index') }}" class="underline font-medium">Transaksi Peminjaman</a>.</p>
            </div>
        @endforelse
    </div>

    @if ($borrowings->hasPages())
        <div class="mt-8">
            {{ $borrowings->links() }}
        </div>
    @endif
@endsection
