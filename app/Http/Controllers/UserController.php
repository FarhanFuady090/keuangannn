<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\unitpendidikan;
use App\Models\Admin;
use App\Models\Yayasan;
use App\Models\tupusat;
use App\Models\tuunit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function indexuser(Request $request)
    {
        $role = $request->get('role', 'all');
        $perPage = $request->get('show', 10); // ambil jumlah item per halaman dari dropdown

        $usersQuery = User::query();

        if ($role !== 'all') {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->paginate($perPage)->withQueryString();
        // dd($users);
        return view('admin.manage-user', compact('users', 'role'));
    }

    function createuser()
    {
        return view('admin.create-user');
    }

    //update data
    public function edituser($id)
    {
        $users = User::findorFail($id); // Mendapatkan data user berdasarkan ID
        return view('admin.update-user', compact('users'));
    }

    public function updateuserr(Request $request, $id)
    {
        $users = User::findorFail($id);

        // Validasi dasar (kamu bisa tambahkan yang lain jika perlu)
        $request->validate([
            'nama_user' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'no_telp' => ['required', 'regex:/^[0-9]+$/', 'min:10'],
            'username' => 'required|string|unique:users,username,' . $id,
            'peran_user' => 'nullable|string|in:admin,tupusat,tuunit,yayasan',
            'namaUnit' => 'nullable|string',
        ]);

        // Update table users
        $users->update([
            'name' => $request->nama_user,
            'email' => $request->email,
            'username' => $request->username,
            'no_telp' => $request->no_telp,
            'role' => $request->peran_user,
            'namaUnit' => $request->namaUnit,
        ]);

        // Update ke tabel sesuai role
        switch ($request->peran_user) {
            case 'admin':
                $admin = Admin::where('id', $id)->first();
                if ($admin) {
                    $admin->update([
                        'name' => $request->nama_user,
                        'email' => $request->email,
                        'username' => $request->username,
                        'role' => $request->peran_user,
                    ]);
                }
                break;

            case 'yayasan':
                $yayasan = Yayasan::where('id', $id)->first();
                if ($yayasan) {
                    $yayasan->update([
                        'name' => $request->nama_user,
                        'email' => $request->email,
                        'username' => $request->username,
                        'role' => $request->peran_user,
                    ]);
                }
                break;

            case 'tupusat':
                $tupusat = Tupusat::where('id', $id)->first();
                if ($tupusat) {
                    $tupusat->update([
                        'name' => $request->nama_user,
                        'email' => $request->email,
                        'username' => $request->username,
                        'role' => $request->peran_user,
                    ]);
                }
                break;

            case 'tuunit':
                $tuunit = Tuunit::where('id', $id)->first();
                if ($tuunit) {
                    $tuunit->update([
                        'name' => $request->nama_user,
                        'email' => $request->email,
                        'username' => $request->username,
                        'role' => $request->peran_user,
                    ]);
                }
                break;
        }
        return redirect()->route('admin.manage-user', $id)->with('success', 'Data User berhasil diperbarui');
    }

    //delete data
    public function deleteuserr($id)
    {
        $users = User::findorFail($id);
        $users->delete();
        return redirect()->route('admin.manage-user', $id)->with('success', 'Data berhasil dihapus');
    }

    // Fungsi untuk menyimpan data user
    public function submituser(Request $request)
    {
        // Validasi request (hapus validasi user_id karena akan di-generate otomatis)
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => ['required', 'regex:/^[0-9]+$/', 'min:10'],
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'peran_user' => 'nullable|string|in:admin,tupusat,tuunit,yayasan',
            'namaUnit' => 'nullable|string',
        ]);

        // Cek User apakah sudah ada
        if (User::where('username', $request->username)->exists()) {
            return redirect()->back()->withErrors(['username' => 'Username telah digunakan.'])->withInput();
        }

        // Cek Email apakah sudah ada
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Email telah digunakan.'])->withInput();
        }

        // Pastikan unit pendidikan valid jika role = tuunit
        if ($validated['peran_user'] === 'tuunit' && empty($validated['namaUnit'])) {
            return redirect()->back()->withErrors(['namaUnit' => 'Unit Pendidikan harus dipilih jika peran adalah TU Unit.']);
        }

        DB::transaction(function () use ($validated) {
            // Simpan user
            $users = User::create([
                'name' => $validated['nama_user'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'username' => $validated['username'],
                'role' => $validated['peran_user'],
                'no_telp' => $validated['no_telp'],
                'namaUnit' => $validated['namaUnit'],
            ]);

            // Simpan data berdasarkan role
            switch ($validated['peran_user']) {
                case 'admin':
                    $admin = Admin::create([
                        'user_id' => $users->id,
                        'name' => $validated['nama_user'],
                        'email' => $validated['email'],
                        'password' => bcrypt($validated['password']),
                        'username' => $validated['username'],
                        'role' => $validated['peran_user'],
                    ]);
                    break;

                case 'yayasan':
                    $yayasan = Yayasan::create([
                        'user_id' => $users->id,
                        'name' => $validated['nama_user'],
                        'email' => $validated['email'],
                        'password' => bcrypt($validated['password']),
                        'username' => $validated['username'],
                        'role' => $validated['peran_user'],
                    ]);
                    break;

                case 'tupusat':
                    Tupusat::create([
                        'user_id' => $users->id,
                        'name' => $validated['nama_user'],
                        'email' => $validated['email'],
                        'password' => bcrypt($validated['password']),
                        'username' => $validated['username'],
                        'role' => $validated['peran_user'],
                    ]);
                    break;

                case 'tuunit':
                    Tuunit::create([
                        'user_id' => $users->id,
                        'name' => $validated['nama_user'],
                        'email' => $validated['email'],
                        'password' => bcrypt($validated['password']),
                        'username' => $validated['username'],
                        'role' => $validated['peran_user'],
                    ]);
                    break;
            }
        });

        return redirect()->route('admin.manage-user')->with('success', 'Data User berhasil ditambahkan!');
    }
}
