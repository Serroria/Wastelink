<?php

// 1. Buat direktori sementara untuk Vercel (karena Vercel read-only)
$directories = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Redirect semua path cache bawaan Laravel ke folder /tmp
$cacheFiles = [
    'VIEW_COMPILED_PATH' => '/tmp/views',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
];

foreach ($cacheFiles as $key => $path) {
    putenv("{$key}={$path}");
    $_ENV[$key] = $path;
    $_SERVER[$key] = $path;
}

// 3. Pastikan file index.php bawaan public Laravel dipanggil
require __DIR__ . '/../public/index.php';
