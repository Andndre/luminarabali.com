<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GeneratePassword extends Command
{
    protected $signature = 'password:generate {password}';
    protected $description = 'Generate a bcrypt hash for a given password';

    public function handle(): int
    {
        $password = $this->argument('password');

        if (! $password) {
            $this->error('Password tidak boleh kosong.');
            return self::FAILURE;
        }

        $hash = Hash::make($password);

        $this->line("Password: {$password}");
        $this->line("Hash: {$hash}");

        return self::SUCCESS;
    }
}
