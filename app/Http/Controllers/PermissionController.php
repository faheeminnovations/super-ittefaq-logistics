<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('pages.permissions', compact('permissions'));
    }

    public function assignPermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $permissions = $request->input('permissions', []);
        
        $user->syncPermissions($permissions);
        
        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }

    public function getUserPermissions($userId)
    {
        $user = User::findOrFail($userId);
        $userPermissions = $user->permissions->pluck('slug')->toArray();
        $allPermissions = Permission::all()->groupBy('module');
        
        return response()->json([
            'user_permissions' => $userPermissions,
            'all_permissions' => $allPermissions,
        ]);
    }
}
