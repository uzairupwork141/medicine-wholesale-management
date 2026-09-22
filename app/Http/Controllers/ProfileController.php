<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show profile page
    public function index()
    {
        $user = Auth::user();

        return view('profile.index', compact('user'));
    }


    // Update profile information
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'Profile updated successfully.');
    }


    // Show change password page
    public function password()
    {
        return view('profile.password');
    }


    // Change password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'current_password'
            ],

            'password' => [
                'required',
                'min:6',
                'confirmed'
            ],
        ]);

        $user = Auth::user();

        $user->password = Hash::make($request->password);

        $user->save();

        return redirect()
            ->route('profile.password')
            ->with('success', 'Password changed successfully.');
    }
}