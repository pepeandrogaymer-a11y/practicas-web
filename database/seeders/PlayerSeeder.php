<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2, 3] as $n) {
            User::updateOrCreate(
                ['email' => "jugador{$n}@example.com"],
                ['name' => "Jugador {$n}", 'password' => 'password', 'role' => 'jugador']
            );
        }
    }
}