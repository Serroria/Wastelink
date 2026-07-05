<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UmkmPartner;

$sri = UmkmPartner::where('store_name', 'like', '%Sri%')->first();
if ($sri) {
    $sri->update([
        'latitude' => -6.1865,
        'longitude' => 106.8715
    ]);
    echo "Sri updated successfully.\n";
} else {
    echo "Sri not found.\n";
}

$joko = UmkmPartner::where('store_name', 'like', '%Joko%')->first();
if ($joko) {
    $joko->update([
        'latitude' => -6.1830,
        'longitude' => 106.8755
    ]);
    echo "Joko updated successfully.\n";
} else {
    echo "Joko not found.\n";
}
