<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{

    protected $signature = 'admin:create {--email=} {--name=} {--password=}';
    protected $description = 'Créer un utilisateur administrateur';

    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Email de l\'administrateur');
        $name = $this->option('name') ?: $this->ask('Nom de l\'administrateur');
        $password = $this->option('password') ?: $this->secret('Mot de passe');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Erreurs de validation :');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Administrateur créé avec succès !");
        $this->info("Email: {$user->email}");
        $this->info("ID: {$user->id}");

        return 0;
    }
}

