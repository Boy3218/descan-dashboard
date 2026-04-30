<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LkeIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Add a default user
        User::factory()->create([
            'name' => 'Admin Descan',
            'email' => 'admin@descan.go.id',
            'password' => bcrypt('password')
        ]);

        // Add default Desas
        \App\Models\Desa::insert([
            ['name' => 'Desa Wonoharjo', 'kecamatan' => 'Wonoharjo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desa Parigi', 'kecamatan' => 'Parigi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desa Ciganjeng', 'kecamatan' => 'Padaherang', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $csvFile = fopen(base_path('../lke_desa.csv'), 'r');
        if ($csvFile !== false) {
            $header = fgetcsv($csvFile);
            $blok = '';
            $aspek = '';
            $nomor = '';
            $judul_seksi = '';
            $urutan = 0;

            // Pattern for sub_nomor (a, b, a1, 1, 2, etc.)
            $subNomorPattern = '/^([a-z]|[0-9])$|^(a[1-9]|b[1-9])$/';

            // Simple parsing to populate DB
            while (($data = fgetcsv($csvFile)) !== false) {
                $col0 = trim($data[0] ?? '');
                $col1 = trim($data[1] ?? '');
                $col2 = trim($data[2] ?? '');

                if (strncmp($col0, 'BLOK', 4) === 0) {
                    $blok = $col0;
                    continue;
                }

                if (strncmp($col0, 'ASPEK', 5) === 0 || preg_match('/^[IVX]+\. /', $col0)) {
                    $aspek = $col0;
                    continue;
                }

                if (is_numeric($col0)) {
                    $nomor = $col0;
                    // If col1 is NOT a sub-number pattern and not empty, it might be the title
                    if ($col1 != '' && !preg_match($subNomorPattern, $col1)) {
                        $judul_seksi = $col1;
                        continue;
                    } elseif ($col1 == '') {
                        $judul_seksi = $col2;
                        continue;
                    } else {
                        // col0 is number, col1 is sub-number. It's a question row.
                        // If we don't have a title for this new number, reset it
                        // (Wait, we'll handle this in the question logic)
                    }
                }

                if ($col2 != '' && strpos($data[3] ?? '', '-->') === false) {
                    // It's a question
                    // Re-check sub_nomor if col0 was empty
                    $targetSubNomor = $col1;
                    if (preg_match($subNomorPattern, $targetSubNomor)) {
                        $indikator = $col2;

                        $opsi = [];
                        if (!empty($data[3])) {
                            $opsi[] = ['label' => $data[3], 'skor' => (int) ($data[4] ?? 0)];
                        }

                        $startPos = ftell($csvFile);
                        while (($next_data = fgetcsv($csvFile)) !== false) {
                            if (empty(trim($next_data[0])) && empty(trim($next_data[1])) && empty(trim($next_data[2])) && !empty(trim($next_data[3]))) {
                                $opsi[] = ['label' => $next_data[3], 'skor' => (int) ($next_data[4] ?? 0)];
                            } else {
                                fseek($csvFile, $startPos);
                                break;
                            }
                            $startPos = ftell($csvFile);
                        }

                        $parsedBlok = strpos($blok, 'III') !== false ? 'III' : (strpos($blok, 'IV') !== false ? 'IV' : (strpos($blok, 'V') !== false ? 'V' : 'M'));
                        if ($parsedBlok == 'V' && empty($aspek)) {
                            $aspek = 'V. LAPORAN AKHIR';
                        }

                        LkeIndicator::create([
                            'blok' => $parsedBlok,
                            'aspek' => $aspek,
                            'nomor' => $nomor,
                            'sub_nomor' => $targetSubNomor,
                            'judul_indikator' => $nomor . ($judul_seksi ? '. ' . $judul_seksi : ''),
                            'indikator' => $indikator,
                            'opsi_jawaban' => $opsi,
                            'max_skor' => count($opsi) > 0 ? max(array_column($opsi, 'skor')) : 0,
                            'urutan' => ++$urutan
                        ]);
                    }
                }
            }
            fclose($csvFile);
        }
    }
}
