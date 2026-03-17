<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) $query->where('role', $request->role);

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'role'     => 'required|in:buyer,admin',
            'password' => 'required|min:8|confirmed',
            'is_active'=> 'boolean',
        ]);
        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['email_verified_at'] = now();

        $user = User::create($data);
        ActivityLog::record('create', "Admin created user: {$user->name} ({$user->role})", $user);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' created.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:buyer,admin',
            'password' => 'nullable|min:8|confirmed',
            'is_active'=> 'boolean',
        ]);

        if (empty($data['password'])) unset($data['password']);
        else $data['password'] = Hash::make($data['password']);

        $data['is_active'] = $request->boolean('is_active', true);
        $user->update($data);
        ActivityLog::record('update', "Admin updated user: {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' updated.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->withErrors(['error' => 'Cannot delete your own account.']);
        $name = $user->name;
        $user->delete();
        ActivityLog::record('delete', "Admin deleted user: {$name}");
        return back()->with('success', "User '{$name}' deleted.");
    }

    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') return back()->withErrors(['error' => 'Cannot deactivate admin accounts.']);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        ActivityLog::record('update', "Admin {$status} user: {$user->name}", $user);
        return back()->with('success', "User {$user->name} has been {$status}.");
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => ['required', 'in:buyer,admin']]);
        if ($user->id === auth()->id()) return back()->withErrors(['error' => 'You cannot change your own role.']);
        $user->update($validated);
        ActivityLog::record('update', "Admin changed role of {$user->name} to {$validated['role']}", $user);
        return back()->with('success', "Role updated to {$validated['role']} for {$user->name}.");
    }
}
