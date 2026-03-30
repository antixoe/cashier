<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class ManagementController extends Controller
{
    public function users()
    {
        $users = User::with('roles')->paginate(12);
        $roles = Role::all();
        return view('management.users', compact('users', 'roles'));
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if ($request->filled('role_id')) {
            $user->roles()->sync([$request->role_id]);
        }

        return back()->with('success', 'User created');
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name])
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if ($request->filled('role_id')) {
            $user->roles()->sync([$request->role_id]);
        } else {
            $user->roles()->detach();
        }

        return back()->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the current authenticated user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }

    public function roleIndex()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('management.roles', compact('roles'));
    }

    public function roleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        Role::create($request->only('name', 'description'));
        return back()->with('success', 'Role created');
    }

    public function roleDestroy($id)
    {
        $role = Role::findOrFail($id);
        
        if (request()->ajax()) {
            $role->delete();
            return response()->json(['success' => true]);
        }

        $role->delete();
        return back()->with('success', 'Role deleted successfully');
    }
}
