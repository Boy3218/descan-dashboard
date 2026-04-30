<?php
$file = fopen('d:/Descan/lke_desa.csv', 'r');
$line = 0;
while (($data = fgetcsv($file)) !== false) {
    $line++;
    if ($line == 21) {
        echo "Row 21 found:\n";
        foreach ($data as $idx => $val) {
            echo "[$idx]: " . bin2hex($val) . " (" . $val . ")\n";
        }
        break;
    }
}
fclose($file);
