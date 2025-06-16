<?php

namespace App\Http\Controllers\yayasan\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\yayasanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('yayasan.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(yayasanRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('yayasan.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(yayasanRequest $request): RedirectResponse
    {
        Auth::guard('yayasan')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended(route('yayasan.login'));
    }
}