<?php

namespace App\Http\Controllers\tuunit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tuunit')->user();

        // Cek unit user dan redirect ke dashboard yang sesuai
        switch ($user->unit) {
            case 'madin':
                return redirect()->route('tuunit.madin.dashboard');
            case 'tpq':
                return redirect()->route('tuunit.tpq.dashboard');
            default:
                // Jika unit tidak dikenali, redirect ke dashboard default atau halaman error
                return redirect()->route('tuunit.login')->with('error', 'Unit tidak dikenali');
        }
    }

    public function manageTransaksi()
    {
        $user = Auth::guard('tuunit')->user();

        // Cek unit user dan redirect ke manage transaksi yang sesuai
        switch ($user->namaUnit) {
            case 'madin':
                return redirect()->route('tuunit.madin.manage-transaksi');
            case 'tpq':
                return redirect()->route('tuunit.tpq.manage-transaksi');
            default:
                return redirect()->route('tuunit.login')->with('error', 'Unit tidak dikenali');
        }
    }

    public function laporanTransaksi()
    {
        $user = Auth::guard('tuunit')->user();

        // Cek unit user dan redirect ke laporan transaksi yang sesuai
        switch ($user->unit) {
            case 'madin':
                return redirect()->route('tuunit.madin.laporan-transaksi');
            case 'tpq':
                return redirect()->route('tuunit.tpq.laporan-transaksi');
            default:
                return redirect()->route('tuunit.login')->with('error', 'Unit tidak dikenali');
        }
    }
}
