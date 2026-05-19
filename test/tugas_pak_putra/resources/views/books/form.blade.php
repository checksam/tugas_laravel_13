@extends('layouts.app')

@section('title', isset($book) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ isset($book) ? '✏️ Edit Buku' : '📕 Tambah Buku Baru' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">{{ isset($book) ? 'Perbarui informasi buku' : 'Isi form di bawah untuk menambah buku baru' }}</p>
        </div>

        <form action="{{ isset($book) ? route('books.update', $book) : route('books.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8 space-y-6" id="book-form">
            @csrf
            @if (isset($book))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" required
                        value="{{ old('title', $book->title ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Masukkan judul buku">
                </div>

                <div>
                    <label for="author" class="block text-sm font-semibold text-slate-700 mb-2">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="author" id="author" required
                        value="{{ old('author', $book->author ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Nama penulis">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="isbn" class="block text-sm font-semibold text-slate-700 mb-2">ISBN</label>
                    <input type="text" name="isbn" id="isbn"
                        value="{{ old('isbn', $book->isbn ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Contoh: 978-3-16-148410-0">
                </div>

                <div>
                    <label for="publisher" class="block text-sm font-semibold text-slate-700 mb-2">Penerbit</label>
                    <input type="text" name="publisher" id="publisher"
                        value="{{ old('publisher', $book->publisher ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Nama penerbit">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="category" class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                    <input type="text" name="category" id="category"
                        value="{{ old('category', $book->category ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Contoh: Fiksi">
                </div>

                <div>
                    <label for="publication_year" class="block text-sm font-semibold text-slate-700 mb-2">Tahun Terbit</label>
                    <input type="number" name="publication_year" id="publication_year" min="1000" max="{{ date('Y') }}"
                        value="{{ old('publication_year', $book->publication_year ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="{{ date('Y') }}">
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-2">Harga (Rp)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0"
                        value="{{ old('price', $book->price ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="0">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="stock" class="block text-sm font-semibold text-slate-700 mb-2">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" id="stock" required min="0"
                        value="{{ old('stock', $book->stock ?? '') }}"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm"
                        placeholder="Jumlah stok">
                </div>

                @if (isset($book))
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tersedia</label>
                        <div class="flex items-center px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-lg font-bold text-emerald-600">{{ $book->available }}</span>
                            <span class="text-slate-400 text-sm ml-2">/ {{ $book->stock }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Diperbarui otomatis berdasarkan peminjaman</p>
                    </div>
                @endif
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 text-sm resize-none"
                    placeholder="Deskripsi singkat tentang buku...">{{ old('description', $book->description ?? '') }}</textarea>
            </div>

            <div class="flex gap-4 justify-end pt-4 border-t border-slate-100">
                <a href="{{ route('books.index') }}" id="btn-cancel"
                   class="px-6 py-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition-colors duration-200 text-sm">
                    Batal
                </a>
                <button type="submit" id="btn-submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white rounded-xl font-medium shadow-md shadow-primary-200 transition-all duration-200 text-sm">
                    {{ isset($book) ? 'Perbarui' : 'Simpan' }} Buku
                </button>
            </div>
        </form>
    </div>
@endsection
