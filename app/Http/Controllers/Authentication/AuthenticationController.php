<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('authentication.login');
    }

    public function authenticate(Request $request)
    {
        try {
            $request->validate([
                'username_or_employee_number' => 'required',
                'password' => 'required',
            ]);

            $username_check = User::whereNull('deleted_at')->where('username', $request->username_or_employee_number)->first();
            $employee_number_check = User::whereNull('deleted_at')->where('employee_number', $request->username_or_employee_number)->first();

            if (!is_null($username_check)) {
                if (Auth::attempt(['username' => $request->username_or_employee_number, 'password' => $request->password], isset($request->remember))) {
                    $request->session()->regenerate();
                    // For Request Url
                    $intended_url = session()->pull('url.intended', route('home'));
                    return redirect()->to($intended_url);
                } else {
                    return redirect()
                        ->back()
                        ->withErrors(['username_or_employee_number' => 'These credentials do not match our records.'])
                        ->withInput();
                }
            } else {
                if (!is_null($employee_number_check)) {
                    if (Auth::attempt(['employee_number' => $request->username_or_employee_number, 'password' => $request->password], isset($request->remember))) {
                        $request->session()->regenerate();
                        // For Request Url
                        $intended_url = session()->pull('url.intended', route('home'));
                        return redirect()->to($intended_url);
                    } else {
                        return redirect()
                            ->back()
                            ->withErrors(['username_or_employee_number' => 'These credentials do not match our records.'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->withErrors(['username_or_employee_number' => 'These credentials do not match our records.'])
                        ->withInput();
                }
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['username_or_employee_number' => $e->getMessage()])
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
