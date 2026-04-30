<?php

/**
 * Forward Vercel requests to the normal Laravel index.php
 */

putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

$appKey = getenv('APP_KEY') ?: $_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? null;
if ($appKey) {
    putenv('APP_KEY=' . $appKey);
}

require __DIR__ . '/../public/index.php';
