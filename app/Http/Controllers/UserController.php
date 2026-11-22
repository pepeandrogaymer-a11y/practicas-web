<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
       $users = User::whereIn('role', ['admin', 'empleado'])->orderBy('name')->paginate(10);


        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Un empleado siempre crea empleados
        $data['role'] = $request->user()->isAdmin() ? $data['role'] : 'empleado';

        User::create($data);

        return redirect()->route('usuarios.index')
            ->with('status', 'Usuario registrado correctamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(Request $request, User $user)
    {
        $this->guardAdminAccount($request, $user);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->guardAdminAccount($request, $user);

        $data = $request->validated();

        // Si dejó la contraseña vacía, no se cambia
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Solo el admin puede cambiar roles
        if (! $request->user()->isAdmin()) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    // --- Solo administrador (protegido por middleware en las rutas) ---

    // Eliminación lógica (Soft Delete)
    public function destroy(Request $request, User $user)
    {
        $this->preventSelfDelete($request, $user);

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('status', 'Usuario deshabilitado (Soft Delete). Puedes recuperarlo desde la papelera.');
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->whereIn('role', ['admin', 'empleado'])->orderBy('name')->paginate(10);
        return view('users.trashed', compact('users'));
    }

    public function restore(User $user)
    {
        $user->restore();

        return redirect()->route('usuarios.trashed')
            ->with('status', 'Usuario restaurado correctamente.');
    }

    // Eliminación permanente
    public function forceDelete(Request $request, User $user)
    {
        $this->preventSelfDelete($request, $user);

        $user->forceDelete();

        return redirect()->route('usuarios.index')
            ->with('status', 'Usuario eliminado permanentemente.');
    }

    // --- Protecciones internas ---

    // Un empleado no puede tocar cuentas de administrador
    private function guardAdminAccount(Request $request, User $target): void
    {
        abort_if(
            ! $request->user()->isAdmin() && $target->isAdmin(),
            403,
            'No puedes modificar cuentas de administrador.'
        );
    }

    private function preventSelfDelete(Request $request, User $target): void
    {
        abort_if($request->user()->is($target), 403, 'No puedes eliminar tu propia cuenta.');
    }
}
    //

