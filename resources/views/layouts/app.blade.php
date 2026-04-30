<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Cantik Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="antialiased">
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 w-full">
                <div class="flex justify-between w-full">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <img src="{{ asset('logo.png') }}" alt="Logo Desa Cantik" class="h-10 w-auto bg-white p-1 rounded-md shadow-sm">
                        <div class="flex flex-col justify-center">
                            <span class="font-bold text-xl leading-none">Desa Cantik</span>
                            <span class="text-[0.65rem] font-bold tracking-widest text-indigo-200 mt-1 uppercase">Kab. Pangandaran</span>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                        <a href="{{ route('lke.index') }}" class="{{ request()->routeIs('lke.*') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Pengisian LKE</a>
                        <a href="{{ route('desa.index') }}" class="{{ request()->routeIs('desa.*') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Kelola Desa</a>
                    </div>
                    <div class="flex items-center sm:hidden">
                        <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Buka menu utama</span>
                            <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-indigo-700 border-t border-indigo-500">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                <a href="{{ route('lke.index') }}" class="{{ request()->routeIs('lke.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Pengisian LKE</a>
                <a href="{{ route('desa.index') }}" class="{{ request()->routeIs('desa.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Kelola Desa</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
