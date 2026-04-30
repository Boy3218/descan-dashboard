<?php

/**
 * Forward Vercel requests to the normal Laravel index.php
 */

echo "Hello dari Vercel! Jika tulisan ini muncul, berarti Vercel PHP berfungsi dengan baik dan error-nya ada di dalam sistem Laravel.";
exit;

ini_set('display_errors', '1');
error_reporting(E_ALL);


putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

require __DIR__ . '/../public/index.php';
