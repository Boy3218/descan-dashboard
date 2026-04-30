@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('lke.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Kembali ke Daftar Indikator</a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Indikator {{ $indicator->nomor }}{{ $indicator->sub_nomor }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $indicator->aspek }}
                </p>
            </div>
            @php
                $isTitleOnly = in_array($indicator->nomor . $indicator->sub_nomor, ['7a', '9a', '14a']);
                $noEvidence = false; // Add this line to fix the error
            @endphp
            @if(!$isTitleOnly)
                @include('components.status-badge', ['status' => $response->status ?? 'pending'])
            @endif
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">KELOMPOK INDIKATOR:</span>
                <h3 class="text-xl font-extrabold text-gray-900">
                    {{ str_ireplace(['BPS Kabupaten/Kota (predefined)', 'BPS Kabupaten/Kota', 'Kabupaten/Kota (predefined)', 'Desa/kelurahan (predefined)', '(predefined)'], ['BPS Kabupaten Pangandaran', 'BPS Kabupaten Pangandaran', 'Kabupaten Pangandaran', $desa->name ?? '', $desa->name ?? ''], $indicator->judul_indikator) }}
                </h3>
            </div>
            
            @php
                $indikatorText = str_ireplace(['BPS Kabupaten/Kota (predefined)', 'BPS Kabupaten/Kota', 'Kabupaten/Kota (predefined)', 'Desa/kelurahan (predefined)', '(predefined)'], ['BPS Kabupaten Pangandaran', 'BPS Kabupaten Pangandaran', 'Kabupaten Pangandaran', $desa->name ?? '', $desa->name ?? ''], $indicator->indikator);
                if ($indicator->nomor == '7' && $indicator->sub_nomor == 'a') {
                    $response2b = \App\Models\LkeResponse::where('desa_id', $desa->id ?? 0)
                        ->whereHas('indicator', function($q) {
                            $q->where('nomor', '2')->where('sub_nomor', 'b');
                        })->first();
                    $hasil2b = $response2b ? $response2b->opsi_terpilih : '[Belum diisi]';
                    $indikatorText = str_replace('" tampilkan hasil input rincian 2b"', '"' . $hasil2b . '"', $indikatorText);
                }
            @endphp
            <p class="text-sm font-semibold text-gray-400 mb-2 uppercase tracking-tighter">Pertanyaan {{ $indicator->nomor }}{{ $indicator->sub_nomor }} :</p>
            <h4 class="text-lg font-bold text-gray-800 mb-8 leading-relaxed">{!! nl2br(e($indikatorText)) !!}</h4>
            
            @if($indicator->penjelasan)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 whitespace-pre-wrap">{{ $indicator->penjelasan }}</p>
                        @if($indicator->bukti_dukung_desc)
                            <p class="text-sm text-blue-700 font-bold mt-2">Dibutuhkan: {{ $indicator->bukti_dukung_desc }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(!$isTitleOnly)
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Ada kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('lke.update', $indicator->id) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="desa_id" value="{{ $selectedDesaId }}">
                <fieldset>
                    @php 
                        $isTextType = Str::contains(strtolower($indicator->indikator), 'tuliskan') 
                                   || empty($indicator->opsi_jawaban) 
                                   || (count($indicator->opsi_jawaban) == 1 && Str::contains(strtolower($indicator->opsi_jawaban[0]['label'] ?? ''), 'tuliskan'));
                    @endphp

                    @if($isTextType)
                        @php 
                            $isNumeric = ($indicator->blok != 'III') && (
                                Str::contains(strtolower($indicator->indikator), ['berapa', 'jumlah', 'rangking', 'kali']) || 
                                Str::contains(strtolower($indicator->opsi_jawaban[0]['label'] ?? ''), ['berapa', 'jumlah', 'rangking', 'kali'])
                            );
                        @endphp
                        <legend class="text-sm font-medium text-gray-900">Jawaban ({{ $isNumeric ? 'Isian Angka' : 'Isian Bebas' }})</legend>
                        <div class="mt-4">
                            @if($isNumeric)
                                <input type="number" name="opsi_terpilih" value="{{ is_numeric($response->opsi_terpilih) ? $response->opsi_terpilih : '' }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3 border" placeholder="Masukan angka...">
                            @else
                                <textarea name="opsi_terpilih" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3 border" placeholder="Tuliskan jawaban di sini...">{{ $response->opsi_terpilih }}</textarea>
                            @endif
                            <p class="mt-2 text-xs text-gray-500 italic">* Indikator isian ini tidak memiliki bobot skor (Skor: 0)</p>
                        </div>
                    @else
                        <legend class="text-sm font-medium text-gray-900">Opsi Jawaban & Skor</legend>
                        <div class="mt-4 space-y-4">
                            @foreach($indicator->opsi_jawaban as $opsi)
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="opsi_{{ $loop->index }}" name="opsi_terpilih" type="radio" value="{{ $opsi['label'] }}" 
                                        {{ $response->opsi_terpilih == $opsi['label'] ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="opsi_{{ $loop->index }}" class="font-medium text-gray-700">{{ $opsi['label'] }} <span class="text-gray-500 ml-2">(Skor: {{ $opsi['skor'] }})</span></label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </fieldset>

                @if(!$noEvidence)
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
                        <label class="block text-sm font-medium text-gray-700">Tautan Bukti Dukung & Keterangan</label>
                        <button type="button" id="addUrlBtn" class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-semibold px-2 py-1 rounded border border-indigo-200">
                            + Tambah Bukti Dukung Lain
                        </button>
                    </div>
                    <div id="urlContainer" class="space-y-4">
                        @php
                            $urls = is_array($response->bukti_dukung_url) ? $response->bukti_dukung_url : (!empty($response->bukti_dukung_url) ? [$response->bukti_dukung_url] : ['']);
                            $keterangans = is_array($response->keterangan) ? $response->keterangan : (!empty($response->keterangan) ? [$response->keterangan] : ['']);
                            if (empty($urls)) $urls = [''];
                        @endphp
                        @foreach($urls as $index => $url)
                        <div class="flex items-start space-x-2 url-row bg-gray-50 p-3 rounded-md border border-gray-100">
                            <div class="flex-grow space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="url" name="bukti_dukung_url[]" value="{{ $url }}" placeholder="https://..." class="border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border rounded-md p-2">
                                    @if(!empty($url))
                                        <a href="{{ $url }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-100 p-2 rounded-md border border-indigo-200" title="Buka Tautan Ini">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif
                                </div>
                                <input type="text" name="keterangan[]" value="{{ $keterangans[$index] ?? '' }}" placeholder="Keterangan dokumen ini (misal: SK Kades 2024)" class="border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border rounded-md p-2">
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700 remove-url-btn mt-2 {{ $index == 0 ? 'invisible' : '' }}" title="Hapus Tautan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-500 italic">* Indikator ini tidak memerlukan tautan bukti dukung.</p>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:justify-end pt-4 sm:space-x-3 space-y-3 sm:space-y-0 mt-4 border-t border-gray-100">
                    @if(!empty($response->id))
                    <button type="button" onclick="openDeleteModal()" class="w-full sm:w-auto inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-600 bg-white hover:bg-red-50 focus:outline-none">
                        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Isian
                    </button>
                    @endif
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Simpan Jawaban
                    </button>
                </div>
            </form>
            @if(!empty($response->id))
            <form id="deleteForm" action="{{ route('lke.destroy', $response->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <!-- Custom Delete Modal -->
            <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDeleteModal()"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Isian</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus isian ini? Semua data jawaban dan bukti dukung untuk indikator ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" onclick="document.getElementById('deleteForm').submit();" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                            <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openDeleteModal() {
                    document.getElementById('deleteModal').classList.remove('hidden');
                }
                function closeDeleteModal() {
                    document.getElementById('deleteModal').classList.add('hidden');
                }
            </script>
            @endif
            @else
            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mt-6">
                <p class="text-sm text-indigo-700">Indikator ini merupakan judul/kelompok pertanyaan dan tidak memerlukan isian. Silakan kembali atau pilih sub-indikator lainnya.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Reviewer Panel -->
    @if(!$isTitleOnly)
    <div class="bg-gray-100 shadow sm:rounded-lg mt-8 p-6">
        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Panel Evaluator/Reviewer</h3>
        <form action="{{ route('lke.updateStatus', $response->id ?? 0) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Status Validasi</label>
                <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 rounded-md">
                    <option value="pending" {{ ($response->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending (Belum Diperiksa)</option>
                    <option value="in-review" {{ ($response->status ?? 'pending') == 'in-review' ? 'selected' : '' }}>In Review (Sedang Diperiksa)</option>
                    <option value="approved" {{ ($response->status ?? 'pending') == 'approved' ? 'selected' : '' }}>Approved (Selesai)</option>
                    <option value="rejected" {{ ($response->status ?? 'pending') == 'rejected' ? 'selected' : '' }}>Rejected (Perlu Perbaikan)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Catatan/Feedback Evaluator</label>
                <textarea name="catatan_reviewer" rows="3" class="mt-1 border p-2 block w-full rounded-md shadow-sm">{{ $response->catatan_reviewer ?? '' }}</textarea>
            </div>
            <button type="submit" {{ empty($response->id) ? 'disabled' : '' }} class="inline-flex justify-center py-2 px-4 border shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-black focus:outline-none">
                Update Status
            </button>
        </form>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlContainer = document.getElementById('urlContainer');
        const addUrlBtn = document.getElementById('addUrlBtn');

        if(addUrlBtn && urlContainer) {
            addUrlBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'flex items-start space-x-2 url-row bg-gray-50 p-3 rounded-md border border-gray-100';
                row.innerHTML = `
                    <div class="flex-grow space-y-2">
                        <input type="url" name="bukti_dukung_url[]" value="" placeholder="https://..." class="border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border rounded-md p-2">
                        <input type="text" name="keterangan[]" value="" placeholder="Keterangan dokumen ini (misal: SK Kades 2024)" class="border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border rounded-md p-2">
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-url-btn mt-2" title="Hapus Tautan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                urlContainer.appendChild(row);

                row.querySelector('.remove-url-btn').addEventListener('click', function() {
                    row.remove();
                });
            });

            // Attach event listener to existing remove buttons
            document.querySelectorAll('.remove-url-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (!btn.classList.contains('invisible')) {
                        btn.closest('.url-row').remove();
                    }
                });
            });
        }
    });
</script>
@endsection
