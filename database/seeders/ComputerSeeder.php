<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComputerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2] as $n) {
            User::updateOrCreate(
                ['email' => "estudiante{$n}@example.com"],
                ['name' => "Estudiante {$n}", 'password' => 'password', 'role' => 'estudiante']
            );
        }

        for ($i = 1; $i <= 8; $i++) {
            $nombre = sprintf('PC-%02d', $i);

            Computer::firstOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => "Equipo de cómputo {$nombre} del laboratorio"]
            );
        }
    }
}