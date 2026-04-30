<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$responses = \App\Models\LkeResponse::whereNotNull('bukti_dukung_url')->get();
foreach($responses as $r) {
    if(!str_starts_with($r->bukti_dukung_url, '[')) {
        $r->bukti_dukung_url = json_encode([$r->bukti_dukung_url]);
        $r->save();
    }
}
echo "Done";
