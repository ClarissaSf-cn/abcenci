<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spl;
use App\Models\SplDetail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SplsExport;

class SplController extends Controller
{
    public function index(Request $request)
    {
        $query = Spl::withCount('details')->orderBy('tanggal', 'desc');

        if ($request->filled('start')) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('tanggal', '<=', $request->end);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('department')) {
            $dept = $request->department;
            $query->whereHas('details', function ($q) use ($dept) {
                $q->whereIn('nik', function ($sub) use ($dept) {
                    $sub->select('nik')->from('employees')->where('pers_subarea_text', $dept);
                });
            });
        }

        $spls = $query->paginate(20);

        $departments = \App\Models\Employee::query()
            ->whereNotNull('pers_subarea_text')
            ->where('pers_subarea_text', '<>', '')
            ->distinct()
            ->pluck('pers_subarea_text')
            ->sort()
            ->values();

        return view('Dashboard.rekap_spl', compact('spls', 'departments'));
    }

    public function show(Spl $spl)
    {
        $spl->load(['details', 'createdBy']);
        return view('pdf.spl', ['spl' => $spl]);
    }

    public function download(Spl $spl)
    {
        $spl->load(['details', 'createdBy']);
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.spl', ['spl' => $spl])->setPaper('a4', 'landscape');
            $dateStr = \Carbon\Carbon::parse($spl->tanggal)->format('Ymd');
            return $pdf->download('SPL_' . $dateStr . '_' . $spl->id . '.pdf');
        }
        return view('pdf.spl', ['spl' => $spl]);
    }

    public function export(Request $request)
    {
        $query = Spl::with('details')->orderBy('tanggal', 'desc');

        if ($request->filled('start')) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('tanggal', '<=', $request->end);
        }
        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }
        if ($request->filled('department')) {
            $dept = $request->department;
            $query->whereHas('details', function ($q) use ($dept) {
                $q->whereIn('nik', function ($sub) use ($dept) {
                    $sub->select('nik')->from('employees')->where('pers_subarea_text', $dept);
                });
            });
        }

        $spls = $query->get();

        $filename = 'rekap_spl_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($spls) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['SPL ID', 'Tanggal', 'Vendor', 'Created By', 'Nik', 'Nama', 'Jabatan', 'Department', 'Jam In', 'Jam Out', 'Total Jam', 'Alasan', 'Keterangan']);

            foreach ($spls as $s) {
                $creator = optional($s->createdBy)->name ?? '';
                foreach ($s->details as $d) {
                    $dept = \App\Models\Employee::where('nik', $d->nik)->value('pers_subarea_text');
                    fputcsv($out, [
                        $s->id,
                        $s->tanggal,
                        $s->vendor,
                        $creator,
                        $d->nik,
                        $d->nama,
                        $d->jabatan,
                        $dept,
                        $d->jam_in,
                        $d->jam_out,
                        $d->total_jam,
                        $d->alasan,
                        $d->keterangan,
                    ]);
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx(Request $request)
    {
        $query = Spl::with('details')->orderBy('tanggal', 'desc');

        if ($request->filled('start')) {
            $query->whereDate('tanggal', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('tanggal', '<=', $request->end);
        }
        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }
        if ($request->filled('department')) {
            $dept = $request->department;
            $query->whereHas('details', function ($q) use ($dept) {
                $q->whereIn('nik', function ($sub) use ($dept) {
                    $sub->select('nik')->from('employees')->where('pers_subarea_text', $dept);
                });
            });
        }

        $spls = $query->get();

        $rows = [];
        foreach ($spls as $s) {
            $creator = optional($s->createdBy)->name ?? '';
            foreach ($s->details as $d) {
                $dept = \App\Models\Employee::where('nik', $d->nik)->value('pers_subarea_text');
                $rows[] = [
                    'SPL ID' => $s->id,
                    'Tanggal' => $s->tanggal,
                    'Vendor' => $s->vendor,
                    'Created By' => $creator,
                    'Nik' => $d->nik,
                    'Nama' => $d->nama,
                    'Jabatan' => $d->jabatan,
                    'Department' => $dept,
                    'Jam In' => $d->jam_in,
                    'Jam Out' => $d->jam_out,
                    'Total Jam' => $d->total_jam,
                    'Alasan' => $d->alasan,
                    'Keterangan' => $d->keterangan,
                ];
            }
        }

        $filename = 'rekap_spl_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SplsExport($rows), $filename);
    }

    public function destroy(Spl $spl)
    {
        $spl->details()->delete();
        $spl->delete();
        return redirect()->to('/dashboard#rekap-spl')->with('success', 'SPL dihapus.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_vendor' => 'nullable|string|max:255',
            'nik' => 'array',
        ]);

        $spl = Spl::create([
            'tanggal' => $request->tanggal,
            'vendor' => $request->nama_vendor,
            'created_by' => Auth::id(),
            'dibuat_oleh' => $request->input('dibuat_oleh') ?: (Auth::check() ? Auth::user()->name : null),
            'ditugaskan_oleh' => $request->input('ditugaskan_oleh'),
            'disetujui_oleh' => $request->input('disetujui_oleh'),
            'diketahui_oleh' => $request->input('diketahui_oleh'),
        ]);

        $niks = $request->input('nik', []);
        $count = count($niks);

        for ($i = 0; $i < $count; $i++) {
            $totalRaw = $request->input('total_jam')[$i] ?? null;
            $total = 0;
            if ($totalRaw !== null && $totalRaw !== '') {

                $total = floatval(str_replace([' Jam', ' jam', 'JAM'], '', $totalRaw));
            }

            $recAwal = $request->input('rencana_awal')[$i] ?? null;
            $recAkhir = $request->input('rencana_akhir')[$i] ?? null;
            $istAwal = $request->input('istirahat_awal')[$i] ?? null;
            $istAkhir = $request->input('istirahat_akhir')[$i] ?? null;

            if ($total <= 0 && !empty($recAwal) && !empty($recAkhir)) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i', substr($recAwal, 0, 5));
                    $end = \Carbon\Carbon::createFromFormat('H:i', substr($recAkhir, 0, 5));
                    if ($end->lessThan($start)) {
                        $end->addDay();
                    }
                    $diffMinutes = $end->diffInMinutes($start);

                    if (!empty($istAwal) && !empty($istAkhir)) {
                        $iStart = \Carbon\Carbon::createFromFormat('H:i', substr($istAwal, 0, 5));
                        $iEnd = \Carbon\Carbon::createFromFormat('H:i', substr($istAkhir, 0, 5));
                        if ($iEnd->lessThan($iStart)) {
                            $iEnd->addDay();
                        }
                        $diffMinutes -= $iEnd->diffInMinutes($iStart);
                    }

                    $total = round(max(0, $diffMinutes / 60), 2);
                } catch (\Exception $e) {

                }
            }

            SplDetail::create([
                'spl_id' => $spl->id,
                'nik' => $niks[$i] ?? null,
                'nama' => $request->input('nama_pekerja')[$i] ?? null,
                'jabatan' => $request->input('jabatan')[$i] ?? null,
                'jam_in' => $request->input('jam_kerja_in')[$i] ?? null,
                'jam_out' => $request->input('jam_kerja_out')[$i] ?? null,
                'lembur_awal' => $recAwal,
                'lembur_akhir' => $recAkhir,
                'istirahat_awal' => $istAwal,
                'istirahat_akhir' => $istAkhir,
                'total_jam' => $total,
                'alasan' => $request->input('alasan')[$i] ?? null,
                'keterangan' => $request->input('keterangan')[$i] ?? null,
            ]);
        }

        if ($request->filled('download') && $request->download == '1') {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.spl', ['spl' => $spl->load(['details', 'createdBy'])])->setPaper('a4', 'landscape');
                $dateStr = \Carbon\Carbon::parse($spl->tanggal)->format('Ymd');
                return $pdf->download('SPL_' . $dateStr . '_' . $spl->id . '.pdf');
            }

            return view('pdf.spl', ['spl' => $spl->load(['details', 'createdBy'])]);
        }

        return redirect()->to('/dashboard#rekap-spl')->with('success', 'SPL tersimpan.');
    }
}
