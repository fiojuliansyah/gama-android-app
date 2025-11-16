<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = 'mobile/home'; 

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->redirectTo);
        }

        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'string' => 'Kolom :attribute harus berupa teks.',
        ]);
    }

    public function username()
    {
        $login = request()->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_nik';
        request()->merge([$field => $login]);
        return $field;
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $response = $this->authenticated($request, Auth::user());
            
            if ($response) {
                return $response;
            }

            return redirect()->intended($this->redirectTo);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        
    }

    public function logout(Request $request)
    {
        $guard = Auth::guard();
        $guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [
            $this->username() => ['Email atau NIK dan password yang Anda masukkan tidak cocok.'],
        ];
        throw ValidationException::withMessages($errors);
    }
}