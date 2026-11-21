<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validar los datos del formulario
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Comprobar credenciales SIN iniciar sesión todavía
        if (! Auth::validate($credentials)) {
            return back()
                ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])
                ->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        // 3. El administrador necesita una segunda etapa
        if ($user->isAdmin()) {
            return $this->startTwoFactor($request, $user);
        }

        // 4. El empleado entra directo
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // Genera el código, lo guarda cifrado, lo envía y redirige a la ruta temporal
    private function startTwoFactor(Request $request, User $user)
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(5),
        ])->save();

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));

        // Guardamos en sesión quién está pendiente de verificar
        $request->session()->regenerate();
        $request->session()->put('two_factor.user_id', $user->id);

        // Ruta temporal firmada: expira a los 5 minutos
        return redirect()->to(
            URL::temporarySignedRoute('two-factor.show', now()->addMinutes(5))
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
