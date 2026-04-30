<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LkeIndicator;

$indicators = LkeIndicator::where('nomor', '3')->get();
foreach ($indicators as $i) {
    echo "ID: " . $i->id . " | Group: " . $i->judul_indikator . " | Question: " . substr($i->indikator, 0, 50) . "...\n";
}
