<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use App\Imports\AttendanceImport;
use App\Exports\AttendanceExport;
use App\Models\Spl;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\ClosingPeriode;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Keamanan: Hanya pengguna yang memiliki Nama, Email, dan NIK yang dapat mengakses dashboard
        if ($user && $user->role === 'karyawan' && empty(trim((string)$user->nik))) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akses Ditolak! Akun Anda belum memiliki NIK yang terdaftar di Manajemen Pengguna. Silakan hubungi Administrator.');
        }

        $witaNow = Carbon::now('Asia/Makassar');
        $todayDate = $witaNow->format('Y-m-d');

        // Log dan statistik realtime selalu mengikuti hari ini secara default, kecuali jika user memilih tanggal lain
        $filterDate = $request->input('tanggal', $request->input('date', $todayDate));

        $stats = $this->calculateDashboardStats($filterDate);
        $countHadir = $stats['countHadir'];
        $countLembur = $stats['countLembur'];
        $countIzin = $stats['countIzin'];
        $countAlpa = $stats['countAlpa'];
        $karyawanAlpa = $stats['karyawanAlpa'];
        $trendLabels = $stats['trendLabels'];
        $trendHadir = $stats['trendHadir'];
        $trendLembur = $stats['trendLembur'];
        $trendIzin = $stats['trendIzin'];
        $trendTanpaKet = $stats['trendTanpaKet'];
        $tim_assorting = $stats['tim_assorting'];
        $targetDateString = $stats['targetDateString'];

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $targetDateString)
            ->whereNull('catatan')
            ->first();

        $start = $request->input('start_date', date('Y-m-01'));
        $end = $request->input('end_date', date('Y-m-t'));

        $startDate = \Carbon\Carbon::parse($start)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($end)->format('Y-m-d');
        $minDate = min($startDate, $endDate);
        $maxDate = max($startDate, $endDate);

        $query = Attendance::whereNotNull('catatan')
            ->whereDate('created_at', '>=', $minDate)
            ->whereDate('created_at', '<=', $maxDate);

        if ($request->filled('dept')) {
            $dept = $request->dept;
            $query->whereHas('employee', function ($q) use ($dept) {
                $q->where('pers_subarea_text', $dept);
            });
        }

        $rekap_semua = $query->orderBy('created_at', 'desc')->get();

        $employees = Employee::orderBy('nama', 'asc')->paginate(15);
        $master_karyawan = Employee::select('nik', 'nama', 'jabatan', 'vendor_os')->orderBy('nama', 'asc')->get();

        $dist = Attendance::whereNotNull('catatan')->whereDate('created_at', $targetDateString)
            ->select('status')->get()->groupBy('status')->map(fn($g) => $g->count());

        $chartLabels = $dist->keys()->toArray();
        $chartValues = $dist->values()->toArray();

        $lemburData = [];
        foreach ($rekap_semua as $absen) {
            if ($absen->status === 'Lembur') {
                $parts = explode(' | ', str_replace('Assorting: ', '', $absen->catatan));
                $nama = explode(' - ', $parts[0])[1] ?? explode(' - ', $parts[0])[0];

                $akm = 0;
                foreach ($parts as $p) {
                    if (str_starts_with(trim($p), 'Akm:')) {
                        $akm = (float) trim(str_replace('Akm:', '', $p));
                    }
                }

                if (!isset($lemburData[trim($nama)])) { $lemburData[trim($nama)] = 0; }
                $lemburData[trim($nama)] += $akm;
            }
        }

        $rekapLembur = collect();
        $gajiPokok = 3496195;
        $upahPerJam = $gajiPokok / 173;

        foreach ($lemburData as $namaPegawai => $totalJam) {
            if ($totalJam > 0) {
                $rekapLembur->push((object) [
                    'nama' => $namaPegawai,
                    'total_jam_lembur' => $totalJam,
                    'upah_per_jam' => $upahPerJam,
                    'total_tagihan' => $totalJam * $upahPerJam
                ]);
            }
        }
        $rekapLembur = $rekapLembur->sortBy('nama');

        $querySpl = Spl::withCount('details')->orderBy('tanggal', 'desc');
        if ($request->filled('start')) {
            $querySpl->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $querySpl->whereDate('tanggal', '<=', $request->end);
        }
        if ($request->filled('vendor')) {
            $querySpl->where('vendor', 'like', '%' . $request->vendor . '%');
        }
        if ($request->filled('department')) {
            $querySpl->whereHas('details', function ($q) use ($request) {
                $dept = $request->department;
                $q->whereIn('nik', function ($sub) use ($dept) {
                    $sub->select('nik')->from('employees')->where('pers_subarea_text', $dept);
                });
            });
        }
        $spls = $querySpl->paginate(20)->withQueryString();
        $departments = Employee::query()
            ->whereNotNull('pers_subarea_text')
            ->where('pers_subarea_text', '<>', '')
            ->distinct()
            ->pluck('pers_subarea_text')
            ->sort()
            ->values();

        $shifts = Shift::orderBy('wst_code', 'asc')->paginate(15)->withQueryString()->fragment('master-shift');

        $riwayat_closing = \App\Models\ClosingPeriode::orderBy('created_at', 'desc')->get();
        $attendance_locations = \App\Models\AttendanceLocation::orderBy('id', 'asc')->get();

        return view('dashboard.dashboard', compact(
            'attendance', 'tim_assorting', 'rekap_semua', 'employees', 'master_karyawan',
            'chartLabels', 'chartValues', 'trendLabels', 'trendHadir',
            'trendLembur', 'trendIzin', 'trendTanpaKet', 'filterDate',
            'rekapLembur', 'countHadir', 'countLembur', 'countIzin', 'countAlpa', 'karyawanAlpa',
            'spls', 'departments', 'shifts',
            'riwayat_closing', 'attendance_locations'
        ));
    }

    /**
     * Hitung statistik kehadiran untuk Dashboard Utama
     */
    public function calculateDashboardStats($filterDate)
    {
        $targetDate = Carbon::parse($filterDate, 'Asia/Makassar');
        $targetDateString = $targetDate->format('Y-m-d');

        $tim_assorting = Attendance::whereNotNull('catatan')
            ->whereDate('created_at', $targetDateString)
            ->orderBy('created_at', 'desc')
            ->get();

        $countHadir = $tim_assorting->where('status', 'Hadir')->count();
        $countLembur = $tim_assorting->where('status', 'Lembur')->count();
        $countIzin = $tim_assorting->whereIn('status', ['Izin', 'Sakit', 'Izin / Sakit'])->count();

        $allMasterEmployees = Employee::select('nik', 'nama', 'jabatan', 'pers_subarea_text', 'vendor_os')->orderBy('nama', 'asc')->get();
        $totalMasterEmployees = $allMasterEmployees->count();

        $attendedNiks = [];
        $manualAlpaNiks = [];
        foreach ($tim_assorting as $att) {
            if (!empty($att->catatan)) {
                $clean = str_replace('Assorting: ', '', $att->catatan);
                $parts = explode(' | ', $clean);
                $firstPart = $parts[0] ?? '';
                $nikNama = explode(' - ', $firstPart);
                $nik = trim($nikNama[0] ?? '');
                if (!empty($nik)) {
                    if ($att->status === 'Tanpa Keterangan') {
                        $manualAlpaNiks[] = $nik;
                    } else {
                        $attendedNiks[] = $nik;
                    }
                }
            }
        }
        $attendedNiks = array_unique($attendedNiks);
        $manualAlpaNiks = array_unique($manualAlpaNiks);

        $karyawanAlpa = $allMasterEmployees->filter(function ($emp) use ($attendedNiks, $manualAlpaNiks) {
            $nik = trim($emp->nik);
            return !in_array($nik, $attendedNiks) || in_array($nik, $manualAlpaNiks);
        })->values();

        $countAlpa = $karyawanAlpa->count();

        $trendLabels = [];
        $trendHadir = [];
        $trendLembur = [];
        $trendIzin = [];
        $trendTanpaKet = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $targetDate->copy()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $trendLabels[] = $date->translatedFormat('d M');
            $hCount = Attendance::whereNotNull('catatan')->whereDate('created_at', $dateString)->where('status', 'Hadir')->count();
            $lCount = Attendance::whereNotNull('catatan')->whereDate('created_at', $dateString)->where('status', 'Lembur')->count();
            $iCount = Attendance::whereNotNull('catatan')->whereDate('created_at', $dateString)->whereIn('status', ['Izin', 'Sakit', 'Izin / Sakit'])->count();

            $dayAttRecords = Attendance::whereNotNull('catatan')->whereDate('created_at', $dateString)->pluck('catatan');
            $dayAttendedNiks = [];
            foreach ($dayAttRecords as $cat) {
                $clean = str_replace('Assorting: ', '', $cat);
                $parts = explode(' | ', $clean);
                $firstPart = $parts[0] ?? '';
                $nikNama = explode(' - ', $firstPart);
                $n = trim($nikNama[0] ?? '');
                if (!empty($n)) {
                    $dayAttendedNiks[$n] = true;
                }
            }
            $tCount = max(0, $totalMasterEmployees - count($dayAttendedNiks));

            $trendHadir[] = $hCount;
            $trendLembur[] = $lCount;
            $trendIzin[] = $iCount;
            $trendTanpaKet[] = $tCount;
        }

        $totalReal = $countHadir + $countLembur + $countIzin + $countAlpa;
        $rasioKehadiran = $totalReal > 0 ? round(($countHadir / $totalReal) * 100) : 0;

        return [
            'countHadir' => $countHadir,
            'countLembur' => $countLembur,
            'countIzin' => $countIzin,
            'countAlpa' => $countAlpa,
            'totalMasterEmployees' => $totalMasterEmployees,
            'totalReal' => $totalReal,
            'rasioKehadiran' => $rasioKehadiran,
            'trendLabels' => $trendLabels,
            'trendHadir' => $trendHadir,
            'trendLembur' => $trendLembur,
            'trendIzin' => $trendIzin,
            'trendTanpaKet' => $trendTanpaKet,
            'karyawanAlpa' => $karyawanAlpa,
            'tim_assorting' => $tim_assorting,
            'targetDateString' => $targetDateString,
            'lastUpdated' => Carbon::now('Asia/Makassar')->format('H:i:s') . ' WITA'
        ];
    }

    /**
     * Endpoint API JSON untuk pembaruan Dashboard Utama secara Realtime
     */
    public function getRealtimeStats(Request $request)
    {
        $witaNow = Carbon::now('Asia/Makassar');
        $todayDate = $witaNow->format('Y-m-d');

        // Selalu realtime mengikuti hari ini secara default
        $filterDate = $request->input('tanggal', $request->input('date', $todayDate));
        $stats = $this->calculateDashboardStats($filterDate);

        // Hapus koleksi besar sebelum dikirim dalam JSON agar respon sangat cepat
        unset($stats['tim_assorting'], $stats['karyawanAlpa']);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function dashboardKaryawan()
    {
        return redirect()->to(route('dashboard') . '#form-absen');
    }

    public function store(Request $request)
    {

        if ($request->action == 'absen_mandiri') {
            $request->validate([
                'id_karyawan' => 'required',
                'nama_karyawan' => 'required',
                'status_kehadiran' => 'required',
                'foto_absen' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);

            $nik = trim($request->id_karyawan);
            $tanggalHariIni = Carbon::today('Asia/Makassar')->format('Y-m-d');

            $loggedUser = Auth::user();
            if ($loggedUser && $loggedUser->role === 'karyawan') {
                if (empty($loggedUser->nik)) {
                    return back()->with('error', 'Akses Ditolak: Akun Anda belum terikat dengan NIK Karyawan paten. Silakan hubungi Administrator.');
                }
                if (trim((string)$loggedUser->nik) !== $nik) {
                    return back()->with('error', "Akses Ditolak: Anda dilarang mengabsenkan orang lain! Akun Anda terdaftar paten hanya untuk NIK {$loggedUser->nik}.");
                }
            } elseif ($loggedUser && $loggedUser->role === 'admin_internal') {
                if (!empty($loggedUser->nik) && trim((string)$loggedUser->nik) !== $nik) {
                    return back()->with('error', "Akses Ditolak: Akun Admin Internal Anda terdaftar paten untuk NIK {$loggedUser->nik}.");
                }
            }

            $karyawanValid = \App\Models\Employee::where('nik', $nik)->first();
            if (!$karyawanValid) {
                return back()->with('error', 'Akses Ditolak: NIK tidak terdaftar di Master Karyawan!');
            }

            $sudahAbsen = Attendance::whereDate('created_at', $tanggalHariIni)
                ->where('status', $request->status_kehadiran)
                ->where('catatan', 'LIKE', '%Assorting: ' . $nik . '%')
                ->exists();

            if ($sudahAbsen) {
                return back()->with('error', "Akses Ditolak: NIK {$nik} sudah melakukan absen {$request->status_kehadiran} hari ini!");
            }

            // Evaluasi Lokasi & Radius Berdasarkan Pengaturan Super Admin
            $activeLocations = \App\Models\AttendanceLocation::where('is_active', true)->get();
            if ($activeLocations->isEmpty()) {
                $activeLocations = \App\Models\AttendanceLocation::all();
            }
            if ($activeLocations->isEmpty()) {
                $activeLocations = collect([
                    (object)[
                        'nama_lokasi' => 'Pabrik PT Indofood CBP Sukses Makmur Tbk',
                        'latitude' => -3.54895,
                        'longitude' => 114.72981,
                        'radius_meter' => 100,
                    ]
                ]);
            }

            $userLat = (float) $request->latitude;
            $userLng = (float) $request->longitude;
            $earthRadius = 6371000;

            $closestLocation = null;
            $minDistance = INF;
            $isWithinAllowedZone = false;
            $matchedBy = 'polygon';

            foreach ($activeLocations as $loc) {
                $latFrom = deg2rad($userLat);
                $lonFrom = deg2rad($userLng);
                $latTo = deg2rad((float) $loc->latitude);
                $lonTo = deg2rad((float) $loc->longitude);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                $dist = $earthRadius * $angle;

                if ($dist < $minDistance) {
                    $minDistance = $dist;
                    $closestLocation = $loc;
                }

                // Cek 1: Validasi batas mutlak Poligon OSS
                $hasPoly = false;
                if (!empty($loc->polygon)) {
                    $polygonCoords = is_string($loc->polygon) ? json_decode($loc->polygon, true) : $loc->polygon;
                    if (is_array($polygonCoords) && count($polygonCoords) >= 3) {
                        $hasPoly = true;
                        if ($this->isPointInPolygon($userLat, $userLng, $polygonCoords)) {
                            $isWithinAllowedZone = true;
                            $matchedBy = 'polygon';
                            $closestLocation = $loc;
                            $minDistance = $dist;
                            break;
                        }
                    }
                }

                // Cek 2: Fallback hanya jika zona lokasi BELUM memiliki poligon sama sekali
                if (!$hasPoly && $dist <= (float) $loc->radius_meter) {
                    $isWithinAllowedZone = true;
                    $matchedBy = 'radius';
                    $closestLocation = $loc;
                    $minDistance = $dist;
                    break;
                }
            }

            if (!$isWithinAllowedZone) {
                $locName = $closestLocation ? $closestLocation->nama_lokasi : 'Kantor Indofood';
                return back()->with('error', "LOKASI TIDAK VALID! Anda terdeteksi berada di luar Zona OSS presensi ({$locName}). Pastikan posisi Anda berada tepat di dalam wilayah kantor.");
            }

            $currentTimeWita = Carbon::now('Asia/Makassar')->format('H:i');
            $batasJamMasuk = '08:00';

            $jamMasuk = '-';
            $jamKeluar = '-';
            $indikatorTelat = '';

            if ($request->status_kehadiran == 'Hadir') {
                $jamMasuk = $currentTimeWita;
                if ($currentTimeWita > $batasJamMasuk) {
                    $indikatorTelat = ' [TELAT]';
                }
            } else if ($request->status_kehadiran == 'Pulang') {
                $jamKeluar = $currentTimeWita;
            }

            $lokasiNama = $closestLocation ? $closestLocation->nama_lokasi : 'Kantor Indofood';
            $lokasiKet = $lokasiNama . ' (Zona OSS)';

            $absen = new Attendance();
            $absen->user_id = Auth::id();
            $absen->status = $request->status_kehadiran;
            $absen->latitude = $userLat;
            $absen->longitude = $userLng;
            $absen->jarak_meter = round($minDistance);
            $absen->lokasi_nama = $lokasiKet;
            $absen->keterangan = $request->keterangan ?? null;

            $koordinatGPS = "[GPS: " . round($userLat, 6) . ", " . round($userLng, 6) . " | " . $absen->lokasi_nama . "]";

            $catatanString = 'Assorting: ' . $nik . ' - ' . $request->nama_karyawan . ' | Shift:- | M:' . $jamMasuk . $indikatorTelat . ' | S:' . $jamKeluar;
            $catatanString .= ' | Ket: ' . ($request->keterangan ?? '-') . ' ' . $koordinatGPS;

            $absen->catatan = $catatanString;

            if ($request->foto_absen) {
                $imgData = $request->foto_absen;
                $image_parts = explode(";base64,", $imgData);
                $image_base64 = base64_decode($image_parts[1]);

                $fileName = 'absen_' . time() . '_' . rand(1000,9999) . '.jpg';
                Storage::disk('public')->put('absensi/' . $fileName, $image_base64);

                $absen->foto = 'absensi/' . $fileName;
            }

            $absen->created_at = Carbon::now('Asia/Makassar');
            $absen->save();

            $targetTab = ($loggedUser && $loggedUser->role === 'karyawan') ? '#form-absen' : '#input';
            return redirect()->to(route('dashboard') . $targetTab)->with('success', "Presensi Valid! Lokasi tercatat di {$absen->lokasi_nama} (Jarak: {$absen->jarak_meter} meter dari titik pusat).");
        }

        if ($request->action == 'assorting_tim') {
            $request->validate(['status_kehadiran' => 'required']);
            $currentTimeWita = Carbon::now('Asia/Makassar')->format('H:i:s');
            $tgl = $request->tanggal ?? Carbon::now('Asia/Makassar')->format('Y-m-d');

            if (is_array($request->id_karyawan)) {
                $savedCount = 0;
                $nikList = $request->id_karyawan;
                $namaList = $request->nama_karyawan ?? [];
                $mulaiList = $request->jam_mulai ?? [];
                $selesaiList = $request->jam_selesai ?? [];
                $istList = $request->istirahat ?? [];
                $riilList = $request->durasi_riil ?? [];
                $akmList = $request->jam_lembur ?? [];
                $ketList = $request->keterangan ?? [];
                $j15List = $request->jam_15 ?? [];
                $j2List = $request->jam_2 ?? [];
                $j3List = $request->jam_3 ?? [];
                $j4List = $request->jam_4 ?? [];

                foreach ($nikList as $idx => $rawNik) {
                    $nik = trim($rawNik);
                    if (empty($nik)) continue;

                    $nama = $namaList[$idx] ?? '-';
                    $mulai = $mulaiList[$idx] ?? '';
                    $selesai = $selesaiList[$idx] ?? '';
                    $ist = $istList[$idx] ?? '0';
                    $riil = $riilList[$idx] ?? '0';
                    $akm = $akmList[$idx] ?? '0';
                    $ket = !empty($ketList[$idx]) ? trim($ketList[$idx]) : 'Lembur';
                    $j15 = $j15List[$idx] ?? '0';
                    $j2 = $j2List[$idx] ?? '0';
                    $j3 = $j3List[$idx] ?? '0';
                    $j4 = $j4List[$idx] ?? '0';

                    $catatan = 'Assorting: ' . $nik . ' - ' . $nama . ' | Shift:- | M:' . $mulai . ' | S:' . $selesai;
                    $catatan .= ' | Akm: ' . $akm . ' | Ket: ' . $ket;
                    $catatan .= " | ~T:{$tgl}|I:{$ist}|R:{$riil}|15:{$j15}|2:{$j2}|3:{$j3}|4:{$j4}~";

                    $absen = new Attendance();
                    $absen->user_id = Auth::id();
                    $absen->status = 'Lembur';
                    $absen->catatan = $catatan;

                    if (!empty($mulai)) {
                        $absen->jam_masuk = $tgl . ' ' . $mulai;
                    }

                    $absen->created_at = $tgl . ' ' . $currentTimeWita;
                    $absen->updated_at = Carbon::now('Asia/Makassar');
                    $absen->save();
                    $savedCount++;
                }

                return redirect()->to(route('dashboard').'?tanggal='.$tgl.'#input-lembur')->with('success', "Berhasil menyimpan {$savedCount} data lembur!");
            }

            $request->validate(['id_karyawan' => 'required', 'nama_karyawan' => 'required']);

            $shift = $request->shift ?? '-';
            $jamMasuk = $request->jam_masuk ?? '-';
            $jamKeluar = $request->jam_keluar ?? '-';
            $indikatorTelat = '';
            $batasJamMasuk = '08:00';

            if ($request->status_kehadiran == 'Hadir' && $jamMasuk !== '-' && !empty($jamMasuk) && $jamMasuk > $batasJamMasuk) {
                $indikatorTelat = ' [TELAT]';
            }

            $catatan = 'Assorting: ' . $request->id_karyawan . ' - ' . $request->nama_karyawan . ' | Shift:' . $shift . ' | M:' . $jamMasuk . $indikatorTelat . ' | S:' . $jamKeluar;

            if ($request->status_kehadiran == 'Lembur') {
                $catatan .= ' | Akm: ' . ($request->jam_lembur ?? 0) . ' | Ket: ' . ($request->keterangan ?? '-');
            }

            $absen = new Attendance();
            $absen->user_id = Auth::id();
            $absen->status = $request->status_kehadiran;
            $absen->catatan = $catatan;

            if($jamMasuk !== '-' && !empty($jamMasuk)) {
                 $absen->jam_masuk = $request->tanggal . ' ' . $jamMasuk;
            }

            $absen->created_at = $request->tanggal . ' ' . $currentTimeWita;
            $absen->updated_at = Carbon::now('Asia/Makassar');
            $absen->save();

            return redirect()->to(route('dashboard').'?tanggal='.$request->tanggal.'#input-lembur')->with('success', 'Data ditambahkan!');
        }
    }

    public function update(Request $request, $id)
    {
        $item = Attendance::findOrFail($id);
        $parts = explode(' | ', str_replace('Assorting: ', '', $item->catatan));
        $idKaryawan = explode(' - ', $parts[0])[0] ?? 'N/A';
        $namaLama = explode(' - ', $parts[0])[1] ?? $request->nama_karyawan;

        $shift = $request->shift ?? '-';
        $jamMasuk = $request->jam_masuk ?? '-';
        $jamKeluar = $request->jam_keluar ?? '-';

        $namaFinal = $request->nama_karyawan ?? $namaLama;
        $catatanBaru = 'Assorting: ' . $idKaryawan . ' - ' . $namaFinal . ' | Shift:' . $shift . ' | M:' . $jamMasuk . ' | S:' . $jamKeluar;

        if ($request->status == 'Lembur') {
            $catatanBaru .= ' | Akm: ' . ($request->jam_lembur ?? 0) . ' | Ket: ' . ($request->keterangan ?? '-');
        }

        $item->update(['status' => $request->status, 'catatan' => $catatanBaru]);
        return redirect()->to(route('dashboard').'#input')->with('success', 'Data diperbarui!');
    }

    public function destroy($id) {
        $item = Attendance::findOrFail($id);
        if($item->foto) { Storage::disk('public')->delete($item->foto); }
        $item->delete();
        return redirect()->to(route('dashboard').'#input')->with('success', 'Data dihapus!');
    }

    public function importExcel(Request $request) {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new AttendanceImport, $request->file('file_excel'));
        return redirect()->to(route('dashboard').'#input')->with('success', 'Data Excel berhasil diimport!');
    }

    public function exportExcel() {
        return Excel::download(new AttendanceExport, 'Rekap_Absensi_' . date('Ymd') . '.xlsx');
    }

    public function storeEmployee(Request $request) {
        $request->validate(['nik' => 'required|unique:employees,nik', 'nama' => 'required']);
        $data = $request->except(['_token', '_method']);
        $jam = trim((string)($data['jam_kerja'] ?? ''));
        $tipeHk = trim((string)($data['tipe_hari_kerja'] ?? ''));
        if (str_starts_with($jam, '=') || empty($jam) || str_contains(strtoupper($jam), 'VLOOKUP')) {
            $data['jam_kerja'] = str_contains($tipeHk, '5') ? '8 Jam' : '7 Jam';
        }
        Employee::create($data);
        return redirect()->to(route('dashboard').'#karyawan')->with('success', 'Karyawan Ditambahkan!');
    }

    public function updateEmployee(Request $request, $id) {
        $emp = Employee::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        $jam = trim((string)($data['jam_kerja'] ?? ''));
        $tipeHk = trim((string)($data['tipe_hari_kerja'] ?? ''));
        if (str_starts_with($jam, '=') || empty($jam) || str_contains(strtoupper($jam), 'VLOOKUP')) {
            $data['jam_kerja'] = str_contains($tipeHk, '5') ? '8 Jam' : '7 Jam';
        }
        $emp->update($data);
        return redirect()->to(route('dashboard').'#karyawan')->with('success', 'Data Diperbarui!');
    }

    public function destroyEmployee($id) {
        Employee::findOrFail($id)->delete();
            return redirect()->to(route('dashboard').'#karyawan')->with('success', 'Hapus Berhasil!');
    }

    public function importEmployee(Request $request) {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv']);
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\EmployeeImport, $request->file('file_excel'));
            return redirect()->to(route('dashboard').'#karyawan')->with('success', 'Master Karyawan Berhasil Diupdate!');
        } catch (\Throwable $e) {
            return redirect()->to(route('dashboard').'#karyawan')->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function paserExcelDate($value) {
        if (empty($value) || trim($value) == '-') return null;
        try {
            if (is_numeric($value)) return Date::excelToDateTimeObject($value)->format('Y-m-d');
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }

    /**
     * Memeriksa apakah koordinat (lat, lng) berada di dalam poligon area geofence.
     * Menggunakan algoritma Ray-Casting.
     */
    private function isPointInPolygon($lat, $lng, $polygon)
    {
        if (!is_array($polygon) || count($polygon) < 3) {
            return false;
        }

        $inside = false;
        $numPoints = count($polygon);

        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = (float) $polygon[$i][0];
            $yi = (float) $polygon[$i][1];
            $xj = (float) $polygon[$j][0];
            $yj = (float) $polygon[$j][1];

            $intersect = (($yi > $lng) != ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
