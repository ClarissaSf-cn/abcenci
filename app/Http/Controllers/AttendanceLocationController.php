<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceLocationController extends Controller
{
    /**
     * Memastikan hanya Super Admin yang bisa mengelola zona lokasi & radius
     */
    private function ensureSuperAdmin()
    {
        $user = Auth::user();
        $uRole = $user ? trim(strtolower($user->role)) : '';
        $uName = $user ? trim(strtoupper($user->name)) : '';

        if ($uRole !== 'superadmin' && $uName !== 'ADMIN SUPER') {
            abort(403, 'Akses Ditolak: Fitur pengaturan lokasi hanya dapat diakses oleh Super Admin.');
        }
    }

    /**
     * Helper untuk memvalidasi dan memformat data poligon koordinat
     */
    private function parsePolygon($polygonInput)
    {
        if (empty($polygonInput)) {
            return null;
        }

        if (is_string($polygonInput)) {
            $decoded = json_decode($polygonInput, true);
        } else {
            $decoded = $polygonInput;
        }

        if (is_array($decoded) && count($decoded) >= 3) {
            $cleanPoints = [];
            foreach ($decoded as $pt) {
                if (is_array($pt) && count($pt) >= 2) {
                    $cleanPoints[] = [(float) $pt[0], (float) $pt[1]];
                }
            }
            return count($cleanPoints) >= 3 ? $cleanPoints : null;
        }

        return null;
    }

    /**
     * API untuk mengambil daftar lokasi aktif (format JSON untuk kiosk / mobile GPS)
     */
    public function getActiveLocations()
    {
        $locations = AttendanceLocation::where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($locations);
    }

    /**
     * Simpan lokasi baru
     */
    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:150',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer',
            'alamat' => 'nullable|string|max:500',
            'polygon' => 'nullable',
        ]);

        $validated['radius_meter'] = $request->filled('radius_meter') ? (int) $request->radius_meter : 100;
        $validated['polygon'] = $this->parsePolygon($request->polygon);
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        AttendanceLocation::create($validated);

        return redirect()->to(route('dashboard') . '#lokasi-absen')->with('success', 'Zona lokasi presensi baru berhasil ditambahkan!');
    }

    /**
     * Update data lokasi
     */
    public function update(Request $request, $id)
    {
        $this->ensureSuperAdmin();

        $location = AttendanceLocation::findOrFail($id);

        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:150',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer',
            'alamat' => 'nullable|string|max:500',
            'polygon' => 'nullable',
        ]);

        $validated['radius_meter'] = $request->filled('radius_meter') ? (int) $request->radius_meter : ($location->radius_meter ?: 100);
        $validated['polygon'] = $this->parsePolygon($request->polygon);
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $location->update($validated);

        return redirect()->to(route('dashboard') . '#lokasi-absen')->with('success', "Zona lokasi '{$location->nama_lokasi}' berhasil diperbarui!");
    }

    /**
     * Toggle status aktif / nonaktif lokasi
     */
    public function toggleStatus($id)
    {
        $this->ensureSuperAdmin();

        $location = AttendanceLocation::findOrFail($id);
        $location->is_active = !$location->is_active;
        $location->save();

        $statusText = $location->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->to(route('dashboard') . '#lokasi-absen')->with('success', "Status zona '{$location->nama_lokasi}' berhasil {$statusText}!");
    }

    /**
     * Hapus lokasi
     */
    public function destroy($id)
    {
        $this->ensureSuperAdmin();

        $location = AttendanceLocation::findOrFail($id);
        $nama = $location->nama_lokasi;
        $location->delete();

        return redirect()->to(route('dashboard') . '#lokasi-absen')->with('success', "Zona lokasi '{$nama}' berhasil dihapus!");
    }
}

