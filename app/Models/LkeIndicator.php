<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LkeIndicator extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'opsi_jawaban' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(LkeResponse::class);
    }
}
