<?php
$file = fopen('d:/Descan/lke_desa.csv', 'r');
$line = 0;
while (($data = fgetcsv($file)) !== false) {
    $line++;
    if ($line >= 18 && $line <= 30) {
        $c0 = $data[0] ?? '';
        $c1 = $data[1] ?? '';
        $c2 = $data[2] ?? '';
        echo "Line $line: col0=[" . $c0 . "] len=" . strlen($c0) . " | col1=[" . $c1 . "] | col2=[" . $c2 . "]\n";
        echo "  is_numeric(trim(col0)): " . (is_numeric(trim($c0)) ? 'true' : 'false') . "\n";
    }
}
fclose($file);
