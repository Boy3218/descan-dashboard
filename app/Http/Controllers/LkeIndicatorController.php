<?php

namespace App\Http\Controllers;

use App\Models\LkeIndicator;
use Illuminate\Http\Request;

class LkeIndicatorController extends Controller
{
    public function index()
    {
        $indicators = LkeIndicator::orderBy('urutan')->get();
        return view('lke_indicator.index', compact('indicators'));
    }

    public function create()
    {
        $lastIndicator = LkeIndicator::orderBy('urutan', 'desc')->first();
        $nextUrutan = $lastIndicator ? $lastIndicator->urutan + 1 : 1;
        return view('lke_indicator.create', compact('nextUrutan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blok' => 'required|string',
            'aspek' => 'required|string',
            'nomor' => 'required|string',
            'sub_nomor' => 'nullable|string',
            'judul_indikator' => 'nullable|string',
            'indikator' => 'required|string',
            'penjelasan' => 'nullable|string',
            'bukti_dukung_desc' => 'nullable|string',
            'urutan' => 'required|integer',
            'opsi_jawaban' => 'nullable|array',
            'opsi_jawaban.*.label' => 'required_with:opsi_jawaban|string',
            'opsi_jawaban.*.skor' => 'required_with:opsi_jawaban|numeric',
        ]);

        // Remove empty options if any
        if (isset($validated['opsi_jawaban'])) {
            $validated['opsi_jawaban'] = array_values(array_filter($validated['opsi_jawaban'], function ($opsi) {
                return !empty($opsi['label']);
            }));
            
            // Calculate max score
            $validated['max_skor'] = count($validated['opsi_jawaban']) > 0 ? max(array_column($validated['opsi_jawaban'], 'skor')) : 0;
        } else {
            $validated['opsi_jawaban'] = [];
            $validated['max_skor'] = 0;
        }

        LkeIndicator::create($validated);

        return redirect()->route('lke-indicator.index')->with('success', 'Kuesioner berhasil ditambahkan.');
    }

    public function edit(LkeIndicator $lke_indicator)
    {
        return view('lke_indicator.edit', compact('lke_indicator'));
    }

    public function update(Request $request, LkeIndicator $lke_indicator)
    {
        $validated = $request->validate([
            'blok' => 'required|string',
            'aspek' => 'required|string',
            'nomor' => 'required|string',
            'sub_nomor' => 'nullable|string',
            'judul_indikator' => 'nullable|string',
            'indikator' => 'required|string',
            'penjelasan' => 'nullable|string',
            'bukti_dukung_desc' => 'nullable|string',
            'urutan' => 'required|integer',
            'opsi_jawaban' => 'nullable|array',
            'opsi_jawaban.*.label' => 'required_with:opsi_jawaban|string',
            'opsi_jawaban.*.skor' => 'required_with:opsi_jawaban|numeric',
        ]);

        if (isset($validated['opsi_jawaban'])) {
            $validated['opsi_jawaban'] = array_values(array_filter($validated['opsi_jawaban'], function ($opsi) {
                return !empty($opsi['label']);
            }));
            
            $validated['max_skor'] = count($validated['opsi_jawaban']) > 0 ? max(array_column($validated['opsi_jawaban'], 'skor')) : 0;
        } else {
            $validated['opsi_jawaban'] = [];
            $validated['max_skor'] = 0;
        }

        $lke_indicator->update($validated);

        return redirect()->route('lke-indicator.index')->with('success', 'Kuesioner berhasil diperbarui.');
    }

    public function destroy(LkeIndicator $lke_indicator)
    {
        $lke_indicator->delete();
        return redirect()->route('lke-indicator.index')->with('success', 'Kuesioner berhasil dihapus.');
    }
}
