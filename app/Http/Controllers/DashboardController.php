<?php

namespace App\Http\Controllers;

use App\Models\LkeIndicator;
use App\Models\LkeResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $desas = \App\Models\Desa::all();
        $selectedDesaId = $request->query('desa_id', $desas->first()->id ?? null);

        $totalIndicators = LkeIndicator::count();
        $responses = LkeResponse::where('desa_id', $selectedDesaId)->get();
        
        $stats = [
            'total_skor' => current_score($responses),
            'progress' => $totalIndicators > 0 ? floor(($responses->where('skor', '>', 0)->count() / $totalIndicators) * 100) : 0,
            'approved' => $responses->where('status', 'approved')->count(),
            'pending' => $totalIndicators - $responses->whereIn('status', ['approved', 'in-review'])->count(),
            'in_review' => $responses->where('status', 'in-review')->count(),
        ];

        $allResponses = LkeResponse::all();
        $allDesaStats = $desas->map(function($desa) use ($totalIndicators, $allResponses) {
            $desaResponses = $allResponses->where('desa_id', $desa->id);
            $approved = $desaResponses->where('status', 'approved')->count();
            $inReview = $desaResponses->where('status', 'in-review')->count();
            $pending = $totalIndicators - $approved - $inReview;
            return [
                'name' => $desa->name,
                'approved' => $approved,
                'in_review' => $inReview,
                'pending' => $pending
            ];
        });

        // Break down by blok
        $indicators = LkeIndicator::with(['responses' => function($q) use ($selectedDesaId) {
            $q->where('desa_id', $selectedDesaId);
        }])->get();

        $desaStats = [
            'total' => $indicators->where('blok', 'III')->count(),
            'filled' => $indicators->where('blok', 'III')->filter(fn($i) => $i->responses->first()?->skor > 0)->count(),
        ];
        $kabStats = [
            'total' => $indicators->where('blok', 'IV')->count(),
            'filled' => $indicators->where('blok', 'IV')->filter(fn($i) => $i->responses->first()?->skor > 0)->count(),
        ];

        return view('dashboard.index', compact('stats', 'desaStats', 'kabStats', 'desas', 'selectedDesaId', 'allDesaStats'));
    }
}

function current_score($responses) {
    $sum = 0;
    foreach($responses as $r) {
        $sum += $r->skor;
    }
    return $sum;
}
