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
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="font-bold text-xl">✨ Desa Cantik</span>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                        <a href="{{ route('lke.index') }}" class="{{ request()->routeIs('lke.*') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Pengisian LKE</a>
                        <a href="{{ route('desa.index') }}" class="{{ request()->routeIs('desa.*') ? 'border-b-2 border-white' : 'text-indigo-100 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Kelola Desa</a>
                    </div>
                </div>
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
