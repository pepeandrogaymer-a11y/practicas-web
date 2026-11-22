<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Lista blanca: solo se puede ordenar por estas columnas
        $ordenables = [
            'nombre' => 'users.name',
            'calificacion' => 'students.calificacion',
            'faltas' => 'students.faltas',
        ];

        $orden = $request->query('orden', 'nombre');
        if (! isset($ordenables[$orden])) {
            $orden = 'nombre';
        }
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $students = Student::query()
            ->join('users', 'users.id', '=', 'students.user_id')
            ->whereNull('users.deleted_at')
            ->select('students.*', 'users.name as nombre', 'users.email')
            ->orderBy($ordenables[$orden], $dir)
            ->paginate(15)
            ->withQueryString();

        return view('alumnos.index', compact('students'));
    }

    public function create()
    {
        return view('alumnos.create');
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'alumno',
            ]);

            $user->student()->create([
                'faltas' => $data['faltas'],
                'calificacion' => $data['calificacion'],
            ]);
        });

        return redirect()->route('alumnos.index')
            ->with('status', 'Alumno registrado correctamente.');
    }

    public function edit(Student $student)
    {
        $student->load('user');

        return view('alumnos.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $student) {
            $userData = ['name' => $data['name'], 'email' => $data['email']];

            if (! empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $student->user->update($userData);

            $student->update([
                'faltas' => $data['faltas'],
                'calificacion' => $data['calificacion'],
            ]);
        });

        return redirect()->route('alumnos.index')
            ->with('status', 'Alumno actualizado correctamente.');
    }

    public function destroy(Student $student)
    {
        // Elimina la cuenta; el registro académico se borra en cascada
        $student->user->forceDelete();

        return redirect()->route('alumnos.index')
            ->with('status', 'Alumno eliminado correctamente.');
    }
}