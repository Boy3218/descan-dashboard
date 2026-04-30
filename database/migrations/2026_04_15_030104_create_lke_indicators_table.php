<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lke_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('blok'); // 'III' (Desa) or 'IV' (Kabkota)
            $table->string('aspek'); // e.g., 'Tata Kelola', 'Kapasitas Statistik'
            $table->string('nomor'); // e.g., '3', '4', '5'
            $table->string('sub_nomor')->nullable(); // e.g., 'a', 'b'
            $table->string('sub_detail')->nullable(); // e.g., '1', '2', '3'
            $table->string('judul_indikator'); // title of question group
            $table->text('indikator'); // full question text
            $table->json('opsi_jawaban'); // JSON [{label, skor}]
            $table->integer('max_skor')->default(0);
            $table->float('bobot')->default(0);
            $table->text('penjelasan')->nullable();
            $table->text('bukti_dukung_desc')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lke_indicators');
    }
};
