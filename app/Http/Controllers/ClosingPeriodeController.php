<?php

namespace App\Http\Controllers;

use App\Models\ClosingPeriode;
use Illuminate\Http\Request;

class ClosingPeriodeController extends Controller
{
    private function assertSuperAdmin(): void
    {
        $user = request()->user();

        $role = strtolower(optional($user)->role ?? '');
        $name = strtoupper(optional($user)->name ?? '');

        if ($role !== 'superadmin' && $name !== 'ADMIN SUPER') {
            abort(403, 'Akses Terbatas: Hanya Super Admin yang diizinkan.');
        }
    }

    public function store(Request $request)
    {
        $this->assertSuperAdmin();

        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required'
        ]);

        $cek = ClosingPeriode::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($cek) {
            return redirect('/dashboard#closing-stationery')
                ->with('error', 'Periode ini sudah dikunci sebelumnya!')
                ->with('active_tab', 'closing-stationery');
        }

        ClosingPeriode::create([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'is_closed' => true,
            'user_id' => request()->user()->id
        ]);

        return redirect('/dashboard#closing-stationery')
            ->with('success', 'Berhasil mengunci periode ' . $request->bulan . '-' . $request->tahun)
            ->with('active_tab', 'closing-stationery');
    }

    public function update($id)
    {
        $this->assertSuperAdmin();

        $periode = ClosingPeriode::findOrFail($id);

        $periode->is_closed = !$periode->is_closed;
        $periode->save();

        $statusText = $periode->is_closed ? 'DIKUNCI' : 'DIBUKA';

        return redirect('/dashboard#closing-stationery')
            ->with('success', "Status periode {$periode->bulan}-{$periode->tahun} berhasil {$statusText}.")
            ->with('active_tab', 'closing-stationery');
    }

    public function destroy($id)
    {
        $this->assertSuperAdmin();

        $periode = ClosingPeriode::findOrFail($id);
        $periode->delete();

        return redirect('/dashboard#closing-stationery')
            ->with('success', 'Riwayat penguncian berhasil dihapus. Transaksi di periode tersebut kini dapat dimanipulasi kembali.')
            ->with('active_tab', 'closing-stationery');
    }
}
