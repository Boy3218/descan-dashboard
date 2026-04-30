@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Kelola Kuesioner LKE</h2>
        <a href="{{ route('lke-indicator.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            + Tambah Pertanyaan
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blok & Aspek</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indikator (Pertanyaan)</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Opsi</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($indicators as $indicator)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $indicator->urutan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="block font-bold text-gray-700">{{ $indicator->blok }}</span>
                            {{ $indicator->aspek }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $indicator->nomor }}{{ $indicator->sub_nomor }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($indicator->indikator, 60) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                            @php
                                $isTextType = Str::contains(strtolower($indicator->indikator), 'tuliskan') 
                                           || empty($indicator->opsi_jawaban) 
                                           || (count($indicator->opsi_jawaban) == 1 && Str::contains(strtolower($indicator->opsi_jawaban[0]['label'] ?? ''), 'tuliskan'));
                            @endphp
                            @if($isTextType)
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Isian Bebas</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ count($indicator->opsi_jawaban ?? []) }} Pilihan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('lke-indicator.edit', $indicator->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('lke-indicator.destroy', $indicator->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini? Ini juga akan menghapus semua jawaban yang terkait!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Belum ada data kuesioner.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
