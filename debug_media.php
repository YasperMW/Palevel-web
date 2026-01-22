<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = app(App\Services\PalevelApiService::class);
    $hostels = $service->getAllHostels();
    if (empty($hostels)) {
        echo "No hostels found\n";
    } else {
        $firstHostel = $hostels[0];
        echo "First Hostel Media:\n";
        print_r($firstHostel['media'] ?? 'No media key');
        
        echo "\nConfig API URL: " . config('palevel.api_url') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
