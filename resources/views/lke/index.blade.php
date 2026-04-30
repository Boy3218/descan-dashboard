@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Daftar Indikator LKE Desa Cantik</h2>
        <div class="flex items-center space-x-4">
            <a href="{{ route('lke.export', ['desa_id' => $selectedDesaId]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
            <form id="desa_form" action="" method="GET" class="flex items-center">
                <label for="desa_select" class="text-sm font-medium text-gray-700 mr-2">Pilih Desa:</label>
                <select id="desa_select" name="desa_id" onchange="document.getElementById('desa_form').submit()" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1 px-3">
                    @foreach($desas as $desa)
                        <option value="{{ $desa->id }}" {{ $selectedDesaId == $desa->id ? 'selected' : '' }}>{{ $desa->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @foreach($grouped as $blok => $aspekGroup)
    <div class="mb-10" id="{{ $blok == 'II' ? 'nominasi' : ($blok == 'III' ? 'desa' : ($blok == 'IV' ? 'kabkota' : 'laporan')) }}">
        <h3 class="text-xl font-bold bg-indigo-600 text-white p-3 rounded-t-lg shadow">
            @if($blok == 'II')
                BLOK II. APAKAH DESA/KELURAHAN AKAN DIAJUKAN DALAM PENILAIAN NOMINASI DESA CANTIK TERBAIK?
            @elseif($blok == 'III')
                BLOK III. PENILAIAN MANDIRI DESA/KELURAHAN
            @elseif($blok == 'IV')
                BLOK IV. PENILAIAN MANDIRI KABUPATEN/KOTA
            @elseif($blok == 'V')
                BLOK V. LAPORAN AKHIR DESA CANTIK
            @else
                BLOK LAINNYA
            @endif
        </h3>
        <div class="bg-white shadow overflow-hidden sm:rounded-b-lg border border-gray-200">
            @foreach($aspekGroup as $aspek => $indicators)
            <div class="border-t border-gray-200">
                <div class="bg-indigo-50 px-4 py-3 font-semibold text-indigo-900 border-b border-indigo-100 uppercase tracking-wider text-xs">
                    ASPEK: {{ $aspek }}
                </div>
                
                @php 
                    $groupedByTitle = $indicators->groupBy('judul_indikator');
                @endphp

                @foreach($groupedByTitle as $title => $subIndicators)
                <div class="bg-gray-100 px-6 py-2 font-bold text-gray-800 border-b border-gray-200 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    {{ str_ireplace(['BPS Kabupaten/Kota (predefined)', 'BPS Kabupaten/Kota', 'Kabupaten/Kota (predefined)', 'Desa/kelurahan (predefined)', '(predefined)'], ['BPS Kabupaten Pangandaran', 'BPS Kabupaten Pangandaran', 'Kabupaten Pangandaran', $desas->find($selectedDesaId)?->name ?? '', $desas->find($selectedDesaId)?->name ?? ''], $title) }}
                </div>
                <ul class="divide-y divide-gray-200">
                    @foreach($subIndicators as $indicator)
                    @php 
                        $response = $indicator->responses->first(); 
                        $isTitleOnly = in_array($indicator->nomor . $indicator->sub_nomor, ['7a', '9a', '14a']);
                    @endphp
                    <li class="p-4 hover:bg-gray-50 transition duration-150 pl-10 border-l-4 border-transparent hover:border-indigo-300">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                @if(!$isTitleOnly)
                                <a href="{{ route('lke.show', ['indicator' => $indicator->id, 'desa_id' => $selectedDesaId]) }}" class="text-gray-900 font-medium hover:text-indigo-600 flex items-start group">
                                    <span class="mr-2 text-indigo-600 font-bold px-1.5 py-0.5 bg-indigo-50 rounded text-xs">{{ $indicator->nomor }}{{ $indicator->sub_nomor }}</span> 
                                    <span class="group-hover:underline">{{ str_ireplace(['BPS Kabupaten/Kota (predefined)', 'BPS Kabupaten/Kota', 'Kabupaten/Kota (predefined)', 'Desa/kelurahan (predefined)', '(predefined)'], ['BPS Kabupaten Pangandaran', 'BPS Kabupaten Pangandaran', 'Kabupaten Pangandaran', $desas->find($selectedDesaId)?->name ?? '', $desas->find($selectedDesaId)?->name ?? ''], $indicator->indikator) }}</span>
                                </a>
                                @else
                                <div class="text-gray-900 font-bold flex items-start">
                                    <span class="mr-2 text-indigo-600 font-bold px-1.5 py-0.5 bg-indigo-50 rounded text-xs">{{ $indicator->nomor }}{{ $indicator->sub_nomor }}</span> 
                                    <span>{{ str_ireplace(['BPS Kabupaten/Kota (predefined)', 'BPS Kabupaten/Kota', 'Kabupaten/Kota (predefined)', 'Desa/kelurahan (predefined)', '(predefined)'], ['BPS Kabupaten Pangandaran', 'BPS Kabupaten Pangandaran', 'Kabupaten Pangandaran', $desas->find($selectedDesaId)?->name ?? '', $desas->find($selectedDesaId)?->name ?? ''], $indicator->indikator) }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="ml-4 flex items-center space-x-4">
                                @if(!$isTitleOnly)
                                @php 
                                    $isTextType = Str::contains(strtolower($indicator->indikator), 'tuliskan') 
                                               || empty($indicator->opsi_jawaban) 
                                               || (count($indicator->opsi_jawaban) == 1 && Str::contains(strtolower($indicator->opsi_jawaban[0]['label'] ?? ''), 'tuliskan'));
                                @endphp

                                @if($response && ($response->skor > 0 || ($isTextType && !empty($response->opsi_terpilih))))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isTextType ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                      {{ $isTextType ? 'Sudah Diisi' : 'Skor: ' . $response->skor }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                      Belum Diisi
                                    </span>
                                @endif

                                @include('components.status-badge', ['status' => $response?->status ?? 'pending'])
                                
                                <a href="{{ route('lke.show', ['indicator' => $indicator->id, 'desa_id' => $selectedDesaId]) }}" class="bg-indigo-600 text-white rounded-md px-4 py-1.5 text-xs font-semibold hover:bg-indigo-700 shadow-sm transition">Isi Bukti</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

</div>
@endsection
