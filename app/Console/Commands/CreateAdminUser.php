<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'bse:create-admin
        {--name=Super Admin : Admin display name}
        {--username=admin : Admin username}
        {--email=admin@admin.com : Admin email}
        {--password= : Admin password}';

    protected $description = 'Create or update the BSE test admin user.';

    public function handle(): int
    {
        $password = (string) ($this->option('password') ?: env('ADMIN_PASSWORD', ''));

        if ($password === '') {
            $this->error('Provide --password=... or set ADMIN_PASSWORD.');
            return self::FAILURE;
        }

        $email = (string) $this->option('email');
        $username = (string) $this->option('username');

        $user = User::query()
            ->where('email', $email)
            ->orWhere('username', $username)
            ->first() ?? new User();

        $user->forceFill([
            'name' => (string) $this->option('name'),
            'username' => $username,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'role' => 'admin',
        ])->save();

        $this->info("Admin user ready: {$user->email}");

        return self::SUCCESS;
    }
}
