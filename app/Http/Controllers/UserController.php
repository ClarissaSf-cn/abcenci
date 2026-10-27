<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:4',
            'role'     => 'required|string',
            'nik'      => $request->role === 'karyawan' ? 'required|string|max:50|unique:users,nik' : 'nullable|string|max:50|unique:users,nik',
        ], [
            'nik.required' => 'NIK wajib diisi untuk pengguna dengan role Karyawan.',
            'nik.unique'   => 'NIK ini sudah terdaftar pada pengguna lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'nik'      => !empty($validated['nik']) ? $validated['nik'] : null,
        ]);

        return redirect()->to(route('dashboard') . '#kelola-user')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:4',
            'role'     => 'required|string',
            'nik'      => $request->role === 'karyawan' ? 'required|string|max:50|unique:users,nik,' . $id : 'nullable|string|max:50|unique:users,nik,' . $id,
        ], [
            'nik.required' => 'NIK wajib diisi untuk pengguna dengan role Karyawan.',
            'nik.unique'   => 'NIK ini sudah terdaftar pada pengguna lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'nik'   => !empty($validated['nik']) ? $validated['nik'] : null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->to(route('dashboard') . '#kelola-user')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->to(route('dashboard') . '#kelola-user')->with('success', 'User berhasil dipindahkan ke tong sampah.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->to(route('dashboard') . '#kelola-user')->with('success', 'User berhasil dikembalikan.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->to(route('dashboard') . '#kelola-user')->with('success', 'User berhasil dihapus permanen.');
    }
}
