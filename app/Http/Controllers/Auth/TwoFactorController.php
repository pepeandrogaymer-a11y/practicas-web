<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class TwoFactorController extends Controller
{
    // Muestra el formulario del código
    public function show()
    {
        return view('auth.two-factor', [
            // La URL del formulario también es temporal y firmada
            'verifyUrl' => URL::temporarySignedRoute('two-factor.verify', now()->addMinutes(5)),
        ]);
    }

    // Comprueba el código
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::find($request->session()->get('two_factor.user_id'));

        // ¿Existe un código vigente?
        if (! $user || ! $user->two_factor_code
            || ! $user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            $request->session()->forget('two_factor.user_id');

            return redirect()->route('login')
                ->withErrors(['email' => 'El código expiró. Inicia sesión de nuevo.']);
        }

        // ¿Es el código correcto?
        if (! Hash::check($request->code, $user->two_factor_code)) {
            return back()->withErrors(['code' => 'El código es incorrecto.']);
        }

        // Todo bien: limpiamos el código (un solo uso) y damos acceso
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        $request->session()->forget('two_factor.user_id');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}