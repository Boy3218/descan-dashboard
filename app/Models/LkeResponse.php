<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LkeResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'bukti_dukung_url' => 'array',
        'keterangan' => 'array',
    ];

    public function indicator()
    {
        return $this->belongsTo(LkeIndicator::class, 'lke_indicator_id');
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
