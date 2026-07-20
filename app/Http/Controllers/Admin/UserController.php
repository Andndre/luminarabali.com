<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Get current user ID and fetch user
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        // Prevent non-super-admin from accessing this via URL hacking
        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'division' => 'required|in:super_admin,designer,photobooth,visual',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'division' => $request->division,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'division' => 'required|in:super_admin,designer,photobooth,visual',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'division' => $request->division,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $currentUserId = Auth::id();
        $currentUser = User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === $currentUserId) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }
}
