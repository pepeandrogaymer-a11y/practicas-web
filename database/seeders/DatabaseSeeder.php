<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Student;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'empleado@example.com'],
            [
                'name' => 'Empleado',
                'password' => 'password',
                'role' => 'empleado',
            ]
        );

                User::updateOrCreate(
            ['email' => 'profesor@example.com'],
            ['name' => 'Profesor', 'password' => 'password', 'role' => 'profesor']
        );

        $alumnos = [
            ['Ana López', 'ana@example.com', 2, 10],
            ['Carlos Ruiz', 'carlos@example.com', 5, 8.5],
            ['Beatriz Soto', 'beatriz@example.com', 12, 6.5],
            ['Diego Marín', 'diego@example.com', 0, 9.2],
        ];

        foreach ($alumnos as [$name, $email, $faltas, $calificacion]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'role' => 'alumno']
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                ['faltas' => $faltas, 'calificacion' => $calificacion]
            );
        }
    }
}