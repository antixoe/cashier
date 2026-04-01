<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;

class SettingsController extends Controller
{
    /**
     * Show user settings/profile page
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $oldData = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $user->update($data);

        // Log activity
        ActivityLogService::log(
            action: 'update_profile',
            description: 'Profile updated',
            modelType: 'User',
            modelId: $user->id,
            oldValues: $oldData,
            newValues: $data
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
        }
        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 400);
            }
            return back()->with('error', 'Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log activity
        ActivityLogService::log(
            action: 'change_password',
            description: 'Password changed',
            modelType: 'User',
            modelId: $user->id
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Password changed successfully']);
        }
        return back()->with('success', 'Password changed successfully');
    }

    /**
     * Get user data for JSON response
     */
    public function getUser()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
