{{-- SESUAIKAN BARIS INI dengan file layout utama Anda (misal: layouts.app atau Dashboard.master) --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h4>📦 Data Pengadaan (PR/PO)</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPengadaan">
            + Tambah Pengadaan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>NO</th>
                        <th>TGL PR</th>
                        <th>NO. PR</th>
                        <th>NAMA BARANG</th>
                        <th>QTY PR</th>
                        <th>NO. PO</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengadaans as $key => $p)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tgl_pr)->format('d M Y') }}</td>
                        <td><strong>{{ $p->no_pr }}</strong></td>
                        <td>{{ $p->stationery->material_name ?? 'Barang Terhapus' }}</td>
                        <td>{{ $p->qty_item }}</td>
                        <td>{{ $p->no_po ?? '-' }}</td>
                        <td>
                            @if($p->status == 'Menunggu')
                                <span class="badge bg-warning text-dark">⏳ Menunggu</span>
                            @else
                                <span class="badge bg-success">✅ Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada data pengadaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahPengadaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pengadaan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Buat Pengadaan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang *</label>
                        <select name="stationery_id" class="form-select" required>
                            <option value="">-- Ketik / Pilih Barang --</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->material_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor PR *</label>
                            <input type="text" name="no_pr" class="form-control" placeholder="Cth: PR-2026-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal PR *</label>
                            <input type="date" name="tgl_pr" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Permintaan (Qty) *</label>
                        <input type="number" name="qty_item" class="form-control" min="1" required>
                    </div>

                    <hr>
                    <p class="text-muted small mb-2"><i>Bagian PO di bawah ini opsional (Bisa diisi nanti jika belum ada).</i></p>

                    <div class="mb-3">
                        <label class="form-label">Nomor PO (Opsional)</label>
                        <input type="text" name="no_po" class="form-control" placeholder="Cth: PO-2026-005">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengadaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
