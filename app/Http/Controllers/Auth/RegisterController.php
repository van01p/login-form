<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function choose()
    {
        return view('auth.register-choose');
    }

    public function showForm(string $role)
    {
        return view('auth.register', ['role' => $role]);
    }

    public function store(Request $request, string $role)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];

        if ($role === 'admin') {
            $rules['admin_code'] = 'required|string';
        }

        $data = $request->validate($rules);

        if ($role === 'admin' && $data['admin_code'] !== env('ADMIN_REGISTER_CODE', 'ADMIN-2026')) {
            return back()
                ->withErrors(['admin_code' => 'Kode admin salah'])
                ->withInput($request->only('name', 'email'));
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => $role,
            'status'   => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $route = in_array($user->role, ['admin', 'seller']) ? 'dashboard' : 'products.index';

        return redirect()->route($route)->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }
}