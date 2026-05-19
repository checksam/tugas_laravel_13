<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Manajemen Perpustakaan - Kelola buku, peminjaman, dan pengembalian dengan mudah.">
    <title>@yield('title', 'Sistem Manajemen Perpustakaan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .nav-link {
            position: relative;
            transition: color 0.2s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #818cf8);
            transition: width 0.3s ease;
            border-radius: 1px;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .flash-message {
            animation: slideDown 0.4s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 min-h-screen">
    <nav class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-slate-200/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <span class="text-2xl group-hover:scale-110 transition-transform duration-200">📚</span>
                        <span class="text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">
                            Perpustakaan
                        </span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    @auth
                        <a href="{{ route('books.index') }}" class="nav-link text-slate-600 hover:text-primary-600 font-medium text-sm">📕 Buku</a>
                        <a href="{{ route('borrowings.index') }}" class="nav-link text-slate-600 hover:text-primary-600 font-medium text-sm">📤 Peminjaman</a>
                        <a href="{{ route('borrowings.history') }}" class="nav-link text-slate-600 hover:text-primary-600 font-medium text-sm">📋 Riwayat</a>
                        <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-slate-200">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-slate-700 text-sm font-medium hidden sm:block">{{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors duration-200 text-sm" title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-primary-600 font-medium text-sm">Login</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="flash-message mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                <div class="flex items-start space-x-3">
                    <span class="text-red-500 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-semibold text-red-800 text-sm">Terjadi Kesalahan:</h3>
                        <ul class="list-disc list-inside text-red-700 mt-1 text-sm space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="flash-message mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm">
                <div class="flex items-center space-x-3">
                    <span class="text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <p class="text-emerald-800 font-medium text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flash-message mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                <div class="flex items-center space-x-3">
                    <span class="text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <p class="text-red-800 font-medium text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white/60 backdrop-blur border-t border-slate-200/60 mt-16">
        <div class="max-w-7xl mx-auto py-6 px-4 text-center">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} Sistem Manajemen Perpustakaan. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
