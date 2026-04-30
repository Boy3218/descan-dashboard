@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="px-4 py-6 sm:px-0">
    <div class="text-center mb-8 bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-10 text-white shadow-xl">
        <h1 class="text-4xl font-extrabold tracking-tight mb-2">Monitoring Desa Cantik 2026</h1>
        <p class="text-xl font-medium opacity-90">Dashboard Perkembangan Pengisian LKE Mandiri</p>
    </div>

    <div class="mb-8 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 items-center bg-white p-5 rounded-xl shadow-md border border-gray-100">
        <label for="desa_select" class="text-gray-700 font-semibold text-lg flex-shrink-0">Pilih Desa / Kelurahan:</label>
        <form id="desa_form" action="" method="GET" class="w-full flex-grow">
            <select id="desa_select" name="desa_id" onchange="document.getElementById('desa_form').submit()" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 py-3 px-4 text-base transition duration-150 ease-in-out bg-gray-50 hover:bg-white cursor-pointer">
                @foreach($desas as $desa)
                    <option value="{{ $desa->id }}" {{ $selectedDesaId == $desa->id ? 'selected' : '' }}>{{ $desa->name }} - Kec. {{ $desa->kecamatan }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl shadow-lg p-6 transform hover:-translate-y-1 transition duration-300">
            <dt class="text-sm font-semibold text-indigo-100 uppercase tracking-wider mb-1">Total Progress LKE</dt>
            <dd class="text-4xl font-extrabold text-white">{{ $stats['progress'] }}%</dd>
            <div class="mt-4 w-full bg-white/30 rounded-full h-1.5 hidden md:block">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $stats['progress'] }}%"></div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl shadow-lg p-6 transform hover:-translate-y-1 transition duration-300">
            <dt class="text-sm font-semibold text-green-100 uppercase tracking-wider mb-1">Indikator Approved</dt>
            <dd class="text-4xl font-extrabold text-white">{{ $stats['approved'] }}</dd>
        </div>

        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg p-6 transform hover:-translate-y-1 transition duration-300">
            <dt class="text-sm font-semibold text-amber-100 uppercase tracking-wider mb-1">Indikator In-Review</dt>
            <dd class="text-4xl font-extrabold text-white">{{ $stats['in_review'] }}</dd>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 transform hover:-translate-y-1 transition duration-300">
            <dt class="text-sm font-semibold text-pink-100 uppercase tracking-wider mb-1">Estimasi Skor</dt>
            <dd class="text-4xl font-extrabold text-white">{{ $stats['total_skor'] }}</dd>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Perbandingan Desa Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6 text-center">Status Indikator Antar Desa</h3>
            <div class="relative h-72 w-full flex justify-center">
                <canvas id="perbandinganChart"></canvas>
            </div>
        </div>

        <!-- Progress Blok Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6 text-center">Progress per Blok LKE</h3>
            <div class="relative h-72 w-full flex justify-center">
                <canvas id="blokChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detail Blocks -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Desa Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 hover:shadow-xl transition duration-300 group">
            <div class="px-6 py-5 font-bold bg-indigo-50/80 border-b border-indigo-100 rounded-t-2xl text-indigo-900 group-hover:bg-indigo-100 transition">
                Blok III: Penilaian Mandiri Desa
            </div>
            <div class="p-8 text-center flex flex-col justify-between h-48">
                <div>
                    <div class="text-5xl font-black text-gray-800 mb-1">{{ $desaStats['filled'] }} <span class="text-2xl text-gray-400 font-medium">/ {{ $desaStats['total'] }}</span></div>
                    <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Indikator Terisi</p>
                </div>
                <a href="{{ route('lke.index', ['desa_id' => $selectedDesaId]) }}#desa" class="inline-flex items-center px-5 py-3 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 hover:scale-105 transition-all w-full justify-center">Ke Laman Pengisian &rarr;</a>
            </div>
        </div>

        <!-- Kabupaten Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 hover:shadow-xl transition duration-300 group">
            <div class="px-6 py-5 font-bold bg-emerald-50/80 border-b border-emerald-100 rounded-t-2xl text-emerald-900 group-hover:bg-emerald-100 transition">
                Blok IV: Penilaian Kab/Kota
            </div>
            <div class="p-8 text-center flex flex-col justify-between h-48">
                <div>
                    <div class="text-5xl font-black text-gray-800 mb-1">{{ $kabStats['filled'] }} <span class="text-2xl text-gray-400 font-medium">/ {{ $kabStats['total'] }}</span></div>
                    <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Indikator Terisi</p>
                </div>
                <a href="{{ route('lke.index', ['desa_id' => $selectedDesaId]) }}#kabkota" class="inline-flex items-center px-5 py-3 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 hover:scale-105 transition-all w-full justify-center">Ke Laman Pengisian &rarr;</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allDesaStats = @json($allDesaStats);
        const desaNames = allDesaStats.map(d => d.name);
        const desaApproved = allDesaStats.map(d => d.approved);
        const desaInReview = allDesaStats.map(d => d.in_review);
        const desaPending = allDesaStats.map(d => d.pending);

        const pCtx = document.getElementById('perbandinganChart').getContext('2d');
        new Chart(pCtx, {
            type: 'bar',
            data: {
                labels: desaNames,
                datasets: [
                    {
                        label: 'Approved',
                        data: desaApproved,
                        backgroundColor: '#10B981',
                        borderRadius: 0,
                        barPercentage: 0.6
                    },
                    {
                        label: 'In-Review',
                        data: desaInReview,
                        backgroundColor: '#F59E0B',
                        borderRadius: 0,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Pending',
                        data: desaPending,
                        backgroundColor: '#EF4444',
                        borderRadius: 0,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [4, 4] }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif", size: 13 }
                        }
                    }
                }
            }
        });

        const bCtx = document.getElementById('blokChart').getContext('2d');
        new Chart(bCtx, {
            type: 'bar',
            data: {
                labels: ['Blok III', 'Blok IV'],
                datasets: [
                    {
                        label: 'Terisi',
                        data: [
                            {{ $desaStats['filled'] }}, 
                            {{ $kabStats['filled'] }}
                        ],
                        backgroundColor: '#6366F1',
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Kosong',
                        data: [
                            {{ $desaStats['total'] - $desaStats['filled'] }}, 
                            {{ $kabStats['total'] - $kabStats['filled'] }}
                        ],
                        backgroundColor: '#F3F4F6',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [4, 4] }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif", size: 13 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
