<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\LkeIndicator;
echo "Count: " . LkeIndicator::count() . "\n";
$first = LkeIndicator::first();
if ($first) {
    echo "First Judul: " . $first->judul_indikator . "\n";
}
