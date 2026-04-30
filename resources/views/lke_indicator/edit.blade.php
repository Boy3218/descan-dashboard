@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-0 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('lke-indicator.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Kembali ke Daftar Kuesioner</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-4">Edit Pertanyaan Kuesioner LKE</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Ada kesalahan pengisian:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lke-indicator.update', $lke_indicator->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Urutan Tampil (Order)</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $lke_indicator->urutan) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Blok</label>
                    <input type="text" name="blok" value="{{ old('blok', $lke_indicator->blok) }}" required placeholder="Contoh: III" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Aspek</label>
                    <input type="text" name="aspek" value="{{ old('aspek', $lke_indicator->aspek) }}" required placeholder="Nama aspek/grup" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nomor</label>
                    <input type="text" name="nomor" value="{{ old('nomor', $lke_indicator->nomor) }}" required placeholder="Contoh: 1, 2, 7" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-700">Sub Nomor / Detail (Opsional)</label>
                    <input type="text" name="sub_nomor" value="{{ old('sub_nomor', $lke_indicator->sub_nomor) }}" placeholder="Contoh: a, b, a1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Judul Indikator / Grup (Opsional)</label>
                    <input type="text" name="judul_indikator" value="{{ old('judul_indikator', $lke_indicator->judul_indikator) }}" placeholder="Contoh: Kelembagaan Desa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Pertanyaan (Indikator) *</label>
                    <textarea name="indikator" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border" placeholder="Masukkan teks pertanyaan kuesioner...">{{ old('indikator', $lke_indicator->indikator) }}</textarea>
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Penjelasan / Bantuan (Opsional)</label>
                    <textarea name="penjelasan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border" placeholder="Catatan tambahan pembantu untuk pengguna...">{{ old('penjelasan', $lke_indicator->penjelasan) }}</textarea>
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Keterangan Bukti Dukung (Opsional)</label>
                    <input type="text" name="bukti_dukung_desc" value="{{ old('bukti_dukung_desc', $lke_indicator->bukti_dukung_desc) }}" placeholder="Contoh: File PDF SK Kepala Desa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>
            </div>

            <!-- Bagian Opsi Jawaban Dinamis -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Opsi Jawaban & Skor</h3>
                        <p class="text-sm text-gray-500">Biarkan kosong jika ini adalah isian bebas teks/angka atau hanya sekadar judul grup indikator.</p>
                    </div>
                    <button type="button" id="addOptionBtn" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-md text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Opsi
                    </button>
                </div>

                <div id="optionsContainer" class="space-y-3">
                    <!-- Javascript will populate this -->
                </div>
            </div>

            <div class="flex justify-end pt-6 space-x-3">
                <a href="{{ route('lke-indicator.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-6 rounded-md shadow-sm focus:outline-none">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('optionsContainer');
        const addBtn = document.getElementById('addOptionBtn');
        let optionCount = 0;

        function addOptionRow(label = '', skor = 0) {
            const row = document.createElement('div');
            row.className = 'flex items-center space-x-4 bg-gray-50 p-3 rounded-md border border-gray-200';
            row.innerHTML = `
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Opsi (Teks Jawaban)</label>
                    <input type="text" name="opsi_jawaban[${optionCount}][label]" value="${label.replace(/"/g, '&quot;')}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Skor Bobot</label>
                    <input type="number" step="0.01" name="opsi_jawaban[${optionCount}][skor]" value="${skor}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2 border">
                </div>
                <div class="mt-5">
                    <button type="button" class="text-red-500 hover:text-red-700 focus:outline-none remove-option-btn p-2" title="Hapus Opsi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            container.appendChild(row);
            
            row.querySelector('.remove-option-btn').addEventListener('click', function() {
                row.remove();
            });

            optionCount++;
        }

        addBtn.addEventListener('click', function() {
            addOptionRow();
        });

        // Initialize with old input or existing database records
        const oldOptions = @json(old('opsi_jawaban', $lke_indicator->opsi_jawaban ?? []));
        if (oldOptions && oldOptions.length > 0) {
            oldOptions.forEach(opt => addOptionRow(opt.label, opt.skor));
        }
    });
</script>
@endsection
