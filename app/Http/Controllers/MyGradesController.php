<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyGradesController extends Controller
{
    public function __invoke(Request $request)
    {
        // Siempre se usa el usuario de la sesión: no hay id en la URL que se pueda cambiar
        $student = $request->user()->student;

        return view('alumnos.mi-informacion', compact('student'));
    }
}
