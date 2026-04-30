<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LkeIndicator;

$indicators = LkeIndicator::where('blok', '!=', 'III')->get();
foreach ($indicators as $i) {
    echo "Block: " . $i->blok . " | Nom: " . $i->nomor . $i->sub_nomor . " | Text: " . $i->indikator . "\n";
    if ($i->opsi_jawaban) {
        foreach ($i->opsi_jawaban as $o) {
            echo "  - " . $o['label'] . "\n";
        }
    }
}
