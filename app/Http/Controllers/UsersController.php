<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        $allUsers = User::all();

        return view('pages.users', [
            'users' => $users,
            'totalUsers' => $allUsers->count(),
            'activeUsers' => $allUsers->where('status', 'active')->count(),
            'suspendedUsers' => $allUsers->where('status', 'suspended')->count(),
            'adminUsers' => $allUsers->where('role', 'admin')->count(),
        ]);
    }

    public function create()
    {
        return view('pages.users-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|max:50',
            'status' => 'required|in:active,suspended',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['user' => $user]);
        }
        
        return view('pages.users-show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($user);
        }
        
        return view('pages.users-edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|max:50',
            'status' => 'required|in:active,suspended',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function permissions(string $id)
    {
        $user = User::findOrFail($id);
        $userPermissions = $user->permissions->pluck('slug')->toArray();
        $allPermissions = \App\Models\Permission::all()->groupBy('module');
        
        return response()->json([
            'user_permissions' => $userPermissions,
            'all_permissions' => $allPermissions,
        ]);
    }

    public function assignPermissions(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $permissions = $request->input('permissions', []);
        
        $user->syncPermissions($permissions);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'Permissions updated successfully.']);
        }
        
        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }
}
