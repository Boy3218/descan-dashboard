<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lke_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lke_indicator_id')->constrained()->onDelete('cascade');
            $table->foreignId('desa_id')->nullable()->constrained('desas')->onDelete('cascade');
            $table->text('opsi_terpilih')->nullable();
            $table->integer('skor')->default(0);
            $table->string('bukti_dukung_path')->nullable();
            $table->string('bukti_dukung_url')->nullable();
            $table->enum('status', ['pending', 'in-review', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_reviewer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lke_responses');
    }
};
