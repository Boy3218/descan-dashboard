<?php

/**
 * Forward Vercel requests to the normal Laravel index.php
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "PHP Version: " . phpversion() . "<br><br>";

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("ERROR FATAL: Folder 'vendor' tidak ditemukan! Artinya Vercel gagal melakukan proses 'composer install' saat deploy. Cek versi PHP Vercel vs Laravel.");
}

putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

$appKey = getenv('APP_KEY') ?: $_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? null;
if (!$appKey) {
    die("<h1>ERROR: Vercel belum membaca APP_KEY!</h1><p>Variabel APP_KEY benar-benar kosong di sistem Vercel. Pastikan Anda mencentang opsi 'Production' saat menambahkan variabel di Vercel.</p>");
} else {
    // Memastikan APP_KEY masuk ke putenv agar bisa dibaca oleh fungsi env() Laravel
    putenv('APP_KEY=' . $appKey);
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Error dari dalam Laravel:</h1>";
    echo "<pre>" . (string) $e . "</pre>";
}
