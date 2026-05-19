@extends('layouts.app')

@section('title', 'Transaksi Peminjaman')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">📤 Transaksi Peminjaman</h1>
                <p class="text-slate-500 mt-1 text-sm">Pinjam buku dari koleksi perpustakaan</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8 space-y-6">
                <form action="{{ route('borrowings.store') }}" method="POST" id="borrow-form">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="book_id" class="block text-sm font-semibold text-slate-700 mb-2">Pilih Buku <span class="text-red-500">*</span></label>
                            <select name="book_id" id="book_id" required onchange="updateBookInfo()"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm bg-white">
                                <option value="">-- Pilih Buku --</option>
                                @foreach ($availableBooks as $book)
                                    <option value="{{ $book->id }}" data-available="{{ $book->available }}" data-title="{{ $book->title }}" data-author="{{ $book->author }}">
                                        {{ $book->title }} — {{ $book->author }} (Tersedia: {{ $book->available }})
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="bookInfo" class="hidden p-4 bg-primary-50/60 rounded-xl border border-primary-100 space-y-1">
                            <p class="text-sm text-slate-600">Judul: <span id="bookTitle" class="font-semibold text-slate-900"></span></p>
                            <p class="text-sm text-slate-600">Penulis: <span id="bookAuthor" class="font-semibold text-slate-900"></span></p>
                            <p class="text-sm text-slate-600">Tersedia: <span id="bookAvailable" class="font-bold text-emerald-600"></span> eksemplar</p>
                        </div>

                        <div>
                            <label for="due_days" class="block text-sm font-semibold text-slate-700 mb-2">Durasi Peminjaman <span class="text-red-500">*</span></label>
                            <select name="due_days" id="due_days" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm bg-white">
                                <option value="">-- Pilih Durasi --</option>
                                <option value="3">3 Hari</option>
                                <option value="7">7 Hari (1 Minggu)</option>
                                <option value="14">14 Hari (2 Minggu)</option>
                                <option value="30">30 Hari (1 Bulan)</option>
                            </select>
                            @error('due_days')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="dueDateInfo" class="hidden p-4 bg-emerald-50/60 rounded-xl border border-emerald-100">
                            <p class="text-sm text-slate-600">📅 Batas Pengembalian: <span id="dueDate" class="font-semibold text-slate-900"></span></p>
                            <p class="text-xs text-slate-400 mt-1">⚠️ Keterlambatan dikenakan denda Rp 5.000/hari</p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm resize-none"
                                placeholder="Contoh: Keperluan tugas kuliah..."></textarea>
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="submit" id="btn-submit-borrow"
                                class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md shadow-emerald-200 hover:shadow-lg transition-all duration-200 text-sm">
                                ✓ Proses Peminjaman
                            </button>
                            <a href="{{ route('borrowings.history') }}" id="btn-view-history"
                               class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-medium text-center transition-colors duration-200 text-sm">
                                📋 Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">📖 Peminjaman Aktif</h2>
                <p class="text-slate-500 text-sm mt-1">Buku yang sedang Anda pinjam</p>
            </div>
            <div class="space-y-4">
                @forelse ($myActiveBorrowings as $borrowing)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 border-l-4 {{ $borrowing->isOverdue() ? 'border-l-red-500' : 'border-l-primary-500' }} hover:shadow-md transition-shadow duration-200">
                        <h3 class="font-semibold text-slate-900 text-sm">{{ $borrowing->book->title }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $borrowing->book->author }}</p>
                        <div class="mt-3 space-y-1">
                            <p class="text-xs text-slate-500">
                                📅 Dipinjam: {{ $borrowing->borrow_date->format('d-m-Y') }}
                            </p>
                            <p class="text-xs {{ $borrowing->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                                ⏰ Kembali: {{ $borrowing->due_date->format('d-m-Y') }}
                                @if ($borrowing->isOverdue())
                                    <span class="inline-flex items-center ml-1 px-2 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-700">
                                        TERLAMBAT {{ abs($borrowing->due_date->diffInDays(now())) }} hari
                                    </span>
                                @else
                                    <span class="text-slate-400">({{ $borrowing->due_date->diffInDays(now()) }} hari lagi)</span>
                                @endif
                            </p>
                        </div>
                        <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" class="mt-4" id="return-form-{{ $borrowing->id }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Kembalikan buku &quot;{{ $borrowing->book->title }}&quot;?')"
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                                📥 Kembalikan
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="bg-primary-50/50 border border-primary-100 rounded-2xl p-6 text-center">
                        <span class="text-3xl mb-2 block">📖</span>
                        <p class="text-primary-700 text-sm font-medium">Tidak ada peminjaman aktif.</p>
                        <p class="text-primary-500 text-xs mt-1">Pilih buku untuk mulai meminjam</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function updateBookInfo() {
            const select = document.getElementById('book_id');
            const option = select.options[select.selectedIndex];
            const bookInfo = document.getElementById('bookInfo');
            const dueDateInfo = document.getElementById('dueDateInfo');

            if (select.value) {
                document.getElementById('bookTitle').textContent = option.dataset.title;
                document.getElementById('bookAuthor').textContent = option.dataset.author;
                document.getElementById('bookAvailable').textContent = option.dataset.available;
                bookInfo.classList.remove('hidden');

                updateDueDate();
            } else {
                bookInfo.classList.add('hidden');
                dueDateInfo.classList.add('hidden');
            }
        }

        function updateDueDate() {
            const daysSelect = document.getElementById('due_days');
            const dueDateInfo = document.getElementById('dueDateInfo');

            if (daysSelect.value) {
                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + parseInt(daysSelect.value));

                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('dueDate').textContent = dueDate.toLocaleDateString('id-ID', options);
                dueDateInfo.classList.remove('hidden');
            } else {
                dueDateInfo.classList.add('hidden');
            }
        }

        document.getElementById('due_days').addEventListener('change', updateDueDate);
    </script>
@endsection
