<?php

namespace App\Http\Controllers\yayasan\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated yayasan's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->yayasan()->hasVerifiedEmail()) {
            return redirect()->intended(route('yayasan.dashboard.index', absolute: false) . '?verified=1');
        }

        if ($request->yayasan()->markEmailAsVerified()) {
            event(new Verified($request->yayasan()));
        }

        return redirect()->intended(route('yayasan.dashboard.index', absolute: false) . '?verified=1');
    }
}
