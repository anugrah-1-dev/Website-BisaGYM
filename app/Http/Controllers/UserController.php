<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Hanya tampilkan user yang punya role admin atau developer
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'developer']);
        })->with('roles')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'developer'])->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name', 'in:admin,developer'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => explode('@', $request->email)[0] . rand(100, 999), // Generate unique username
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        \App\Models\ActivityLog::log('CREATE', 'Manajemen User', "Membuat akun baru: {$user->name} dengan role {$request->role}");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['admin', 'developer'])->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'exists:roles,name', 'in:admin,developer'],
        ]);

        $oldRole = $user->roles->first()?->name;

        $user->name = $request->name;
        $user->email = $request->email;

        $passwordChanged = false;
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
            $passwordChanged = true;
        }

        $user->save();

        // Update role
        $user->syncRoles([$request->role]);

        $desc = "Memperbarui akun {$user->name}";
        if ($oldRole != $request->role) $desc .= " (Role dari $oldRole ke {$request->role})";
        if ($passwordChanged) $desc .= " (Mengganti password)";

        \App\Models\ActivityLog::log('UPDATE', 'Manajemen User', $desc);

        return redirect()->route('users.index')->with('success', 'User berhasil diubah!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login!');
        }

        $name = $user->name;
        $user->delete();

        \App\Models\ActivityLog::log('DELETE', 'Manajemen User', "Menghapus akun user: {$name}");

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
