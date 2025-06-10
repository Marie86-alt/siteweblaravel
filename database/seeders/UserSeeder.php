<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
     public function run(): void
    {
        // Administrateur principal
        User::create([
            'name' => 'Admin Fruits & Légumes',
            'email' => 'admin@fruits-legumes.fr',
            'password' => Hash::make('admin123'),
            'phone' => '+33123456789',
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        // Administrateur de test
        User::create([
            'name' => 'Marie Dubois',
            'email' => 'marie.admin@fruits-legumes.fr',
            'password' => Hash::make('password123'),
            'phone' => '+33123456790',
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        // Clients de test
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456791',
            'birth_date' => '1985-03-15',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Sophie Martin',
            'email' => 'sophie.martin@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456792',
            'birth_date' => '1990-07-22',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Pierre Durand',
            'email' => 'pierre.durand@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456793',
            'birth_date' => '1978-11-08',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Claire Leroy',
            'email' => 'claire.leroy@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456794',
            'birth_date' => '1995-01-30',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Luc Moreau',
            'email' => 'luc.moreau@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456795',
            'birth_date' => '1982-05-12',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Isabelle Blanc',
            'email' => 'isabelle.blanc@email.com',
            'password' => Hash::make('password123'),
            'phone' => '+33123456796',
            'birth_date' => '1987-09-18',
            'is_admin' => false,
            'email_verified_at' => now()
        ]);
    }
}
