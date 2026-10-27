<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman login Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback dari Google setelah otentikasi.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleEmail = trim((string)$googleUser->getEmail());

            // Cari user berdasarkan email di tabel users (Manajemen Pengguna)
            $findUser = User::where('email', $googleEmail)
                ->orWhereRaw('LOWER(email) = ?', [strtolower($googleEmail)])
                ->first();

            // Batasan 1: Jika user baru langsung klik Google dan belum terdaftar di Manajemen Pengguna
            if (!$findUser) {
                return redirect()->route('login')->with(
                    'error',
                    'Akses Ditolak! Akun Google Anda (' . $googleEmail . ') belum terdaftar di Manajemen Pengguna. Anda tidak dapat masuk sebelum akun (Nama, Email, dan NIK) didaftarkan oleh Administrator.'
                );
            }

            // Batasan 2: User harus memiliki data Nama, Email, dan NIK yang terdaftar di Manajemen Pengguna
            $hasName = !empty(trim((string)$findUser->name));
            $hasNik = !empty(trim((string)$findUser->nik));
            $isSuperAdmin = strtolower((string)$findUser->role) === 'superadmin';

            if (!$hasName || (!$hasNik && !$isSuperAdmin)) {
                return redirect()->route('login')->with(
                    'error',
                    'Akses Ditolak! Data akun Anda (' . $googleEmail . ') belum lengkap di Manajemen Pengguna. Nama dan NIK wajib terdaftar. Silakan hubungi Administrator untuk melengkapi data Anda.'
                );
            }

            // Simpan google_id jika belum tersimpan
            if (empty($findUser->google_id)) {
                $findUser->google_id = $googleUser->getId();
                $findUser->save();
            }

            // Login akun
            Auth::login($findUser);
            request()->session()->regenerate();

            // Arahkan ke dashboard sesuai role masing-masing
            if ($findUser->role === 'karyawan') {
                return redirect()->intended(route('dashboard') . '#form-absen');
            }
            if ($findUser->role === 'admin_internal') {
                return redirect()->intended(route('dashboard') . '#input');
            }
            if ($findUser->role === 'admin_outsourcing') {
                return redirect()->intended(route('dashboard') . '#rekap-spl');
            }

            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kendala saat login dengan Google: ' . $e->getMessage());
        }
    }
}
