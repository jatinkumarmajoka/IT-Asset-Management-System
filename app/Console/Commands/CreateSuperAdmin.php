<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin';

    protected $description = 'Create the first Super Admin account';

    public function handle(): int
    {
        if (User::where('role', 'super_admin')->exists()) {
            $this->error('A Super Admin account already exists.');
            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Super Admin name'));
        $email = strtolower(trim((string) $this->ask('Super Admin email')));
        $password = (string) $this->secret('Super Admin password');
        $confirmation = (string) $this->secret('Confirm password');

        if ($password !== $confirmation) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'account_status' => 'approved',
            'permissions' => null,
        ]);

        $this->info('Super Admin created successfully.');
        $this->line('You can now sign in using the email you registered.');

        return self::SUCCESS;
    }
}
