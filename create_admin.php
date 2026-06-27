<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::updateOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Super Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]
    );

    echo "Successfully created Admin account!\nEmail: admin@admin.com\nPassword: password\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
