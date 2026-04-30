<?php

namespace App\Http\Controllers;

use App\Models\LkeIndicator;
use App\Models\LkeResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LkeController extends Controller
{
    public function index(Request $request)
    {
        $desas = \App\Models\Desa::all();
        // default to first desa if none selected
        $selectedDesaId = $request->query('desa_id', $desas->first()->id ?? null);
        
        $indicators = LkeIndicator::orderBy('urutan')
            ->where('blok', '!=', 'V')
            ->with(['responses' => function($q) use ($selectedDesaId) {
                $q->where('desa_id', $selectedDesaId);
            }])->get();
        // Group by Blok and Aspek
        $grouped = $indicators->groupBy(['blok', 'aspek']);
        return view('lke.index', compact('grouped', 'desas', 'selectedDesaId'));
    }

    public function show(Request $request, LkeIndicator $indicator)
    {
        $selectedDesaId = $request->query('desa_id');
        $desa = \App\Models\Desa::find($selectedDesaId);
        $response = $indicator->responses()->where('desa_id', $selectedDesaId)->first() ?? new LkeResponse(['desa_id' => $selectedDesaId]);
        return view('lke.show', compact('indicator', 'response', 'selectedDesaId', 'desa'));
    }

    public function update(Request $request, LkeIndicator $indicator)
    {
        // Simple logic for update
        $request->validate([
            'opsi_terpilih' => 'nullable|string',
            'bukti_dukung_url' => 'nullable|array',
            'bukti_dukung_url.*' => 'nullable|url',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string',
            'desa_id' => 'required|exists:desas,id'
        ]);

        $response = LkeResponse::firstOrNew(['lke_indicator_id' => $indicator->id, 'desa_id' => $request->desa_id]);
        $response->opsi_terpilih = $request->opsi_terpilih;
        
        // Calculate score based on selected ops (assuming JSON structure contains skor)
        $skor = 0;
        $isTextType = Str::contains(strtolower($indicator->indikator), 'tuliskan') 
                   || empty($indicator->opsi_jawaban) 
                   || (count($indicator->opsi_jawaban) == 1 && Str::contains(strtolower($indicator->opsi_jawaban[0]['label'] ?? ''), 'tuliskan'));
        
        if (!$isTextType) {
            foreach($indicator->opsi_jawaban as $opsi) {
                if(isset($opsi['label']) && $opsi['label'] == $request->opsi_terpilih) {
                    $skor = $opsi['skor'] ?? 0;
                }
            }
        }
        $response->skor = $skor;

        if ($request->has('bukti_dukung_url')) {
            $urls = $request->bukti_dukung_url ?? [];
            $keterangans = $request->keterangan ?? [];
            $filteredUrls = [];
            $filteredKeterangans = [];
            
            foreach($urls as $i => $url) {
                if(!empty($url)) {
                    $filteredUrls[] = $url;
                    $filteredKeterangans[] = $keterangans[$i] ?? '';
                }
            }

            if (count($filteredUrls) > 0) {
                $response->bukti_dukung_url = $filteredUrls;
                $response->keterangan = $filteredKeterangans;
                $response->status = 'in-review';
            } else {
                $response->bukti_dukung_url = [];
                $response->keterangan = [];
            }
        }

        if ($request->filled('opsi_terpilih') && empty($response->status) || $response->status == 'pending') {
            $response->status = 'in-review';
        }

        $response->save();

        return redirect()->route('lke.index')->with('success', 'Data berhasil diperbarui');
    }

    public function updateStatus(Request $request, LkeResponse $response)
    {
        $request->validate([
            'status' => 'required|in:pending,in-review,approved,rejected',
            'catatan_reviewer' => 'nullable|string'
        ]);

        $response->status = $request->status;
        $response->catatan_reviewer = $request->catatan_reviewer;
        $response->save();

        return back()->with('success', 'Status berhasil diperbarui');
    }

    public function destroy(LkeResponse $response)
    {
        $response->delete();
        return redirect()->route('lke.index')->with('success', 'Isian berhasil dihapus');
    }

    public function export(Request $request)
    {
        // If a specific desa_id is provided we can filter, else export all
        $query = LkeResponse::with(['indicator', 'desa']);
        if ($request->filled('desa_id')) {
            $query->where('desa_id', $request->desa_id);
        }
        $responses = $query->get();
        
        $filename = "Export_Monitoring_LKE_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['Nama Desa', 'Kecamatan', 'Blok', 'Aspek', 'No', 'Indikator', 'Jawaban', 'Skor', 'Bukti Dukung URL', 'Keterangan', 'Status', 'Catatan Reviewer', 'Waktu Update'];

        $callback = function() use($responses, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel support
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($responses as $response) {
                $indicator = $response->indicator;
                $desa = $response->desa;
                
                $buktiStr = '';
                $ketStr = '';
                if (is_array($response->bukti_dukung_url)) {
                    $urls = $response->bukti_dukung_url;
                    $kets = is_array($response->keterangan) ? $response->keterangan : [];
                    $combinedUrls = [];
                    foreach($urls as $i => $url) {
                        $k = $kets[$i] ?? '';
                        if ($k) {
                            $combinedUrls[] = $url . " (" . $k . ")";
                        } else {
                            $combinedUrls[] = $url;
                        }
                    }
                    $buktiStr = implode(", ", $combinedUrls);
                    $ketStr = implode(", ", $kets);
                } else {
                    $buktiStr = $response->bukti_dukung_url ?? '';
                    $ketStr = $response->keterangan ?? '';
                    if (!is_string($ketStr)) $ketStr = '';
                }

                $row = [
                    $desa->name ?? 'N/A',
                    $desa->kecamatan ?? 'N/A',
                    $indicator->blok ?? '',
                    $indicator->aspek ?? '',
                    ($indicator->nomor ?? '') . ($indicator->sub_nomor ?? ''),
                    $indicator->indikator ?? '',
                    $response->opsi_terpilih ?? '',
                    $response->skor ?? 0,
                    $buktiStr,
                    $ketStr,
                    $response->status ?? '',
                    $response->catatan_reviewer ?? '',
                    $response->updated_at ? $response->updated_at->format('Y-m-d H:i:s') : ''
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
