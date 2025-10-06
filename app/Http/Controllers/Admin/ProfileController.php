<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile form.
     */
    public function index(): View
    {
        $data['title'] = 'Profile';
        $data['user'] = Auth::user();
        return view('admin.profile.index', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            // Update user data
            $user->update($data);

            notyf()->success('Profile updated successfully!');
            return redirect()->route('admin.profile.index');

        } catch (\Exception $e) {
            notyf()->error('Failed to update profile. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the change password form.
     */
    public function changePasswordForm(): View
    {
        $data['title'] = 'Change Password';
        return view('admin.profile.change-password', $data);
    }

    /**
     * Update the user's password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                notyf()->error('Current password is incorrect.');
                return redirect()->back()->withInput();
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            notyf()->success('Password changed successfully!');
            return redirect()->route('admin.profile.change-password');

        } catch (\Exception $e) {
            notyf()->error('Failed to change password. Please try again.');
            return redirect()->back()->withInput();
        }
    }
}
