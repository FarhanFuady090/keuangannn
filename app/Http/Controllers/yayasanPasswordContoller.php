<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class yayasanPasswordController extends Controller
{
    /**
     * Update the password for the user.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validate the new password length...

        $request->user()->fill([
            'password' => Hash::make($request->newPassword)
        ])->save();

        $hashed = Hash::make('password', [
            'rounds' => 12,
        ]);

        $hashed = Hash::make('password', [
            'memory' => 1024,
            'time' => 2,
            'threads' => 2,
        ]);

        if (Hash::check('plain-text', $hashed)) {
            // The passwords match...
        }

        if (Hash::needsRehash($hashed)) {
            $hashed = Hash::make('plain-text');
        }

        return redirect(to: '/yayasan/dashboard');
    }
}
