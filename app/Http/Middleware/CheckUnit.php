<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUnit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredUnit): Response
    {
        $user = Auth::guard('tuunit')->user();

        // Cek apakah user memiliki unit yang sesuai
        if ($user->namaUnit !== $requiredUnit) {
            // Redirect ke dashboard yang sesuai dengan unit user
            switch ($user->namaUnit) {
                case 'madin':
                    return redirect()->route('tuunit.madin.dashboard');
                case 'tpq':
                    return redirect()->route('tuunit.tpq.dashboard');
                case 'tk':
                    return redirect()->route('tuunit.tk.dashboard');
                case 'sd':
                    return redirect()->route('tuunit.sd.dashboard');
                case 'smp':
                    return redirect()->route('tuunit.smp.dashboard');
                case 'sma':
                    return redirect()->route('tuunit.sma.dashboard');
                case 'pondok':
                    return redirect()->route('tuunit.pondok.dashboard');
                default:
                    return redirect()->route('tuunit.login')->with('error', 'Akses ditolak');
            }
        }

        return $next($request);
    }
}
