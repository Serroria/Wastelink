<?php

// Buat direktori sementara untuk Vercel (karena Vercel read-only)
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

// Redirect path Laravel ke folder /tmp
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
putenv('VIEW_COMPILED_PATH=/tmp/views');

// Pastikan file index.php bawaan public Laravel dipanggil
require __DIR__ . '/../public/index.php';
