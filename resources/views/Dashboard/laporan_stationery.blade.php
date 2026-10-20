@extends('layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary font-weight-bold">Laporan Stationery</h3>
            <p class="text-muted">Rekapitulasi Kebutuhan Order dan Histori Pemakaian Barang</p>
        </div>
    </div>

    <div class="card mb-5 shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
            <h5 class="m-0 font-weight-bold text-dark">1️⃣ Order Barang (Stok & Reorder)</h5>
            <a href="{{ route('stationery.export.order') }}" class="btn btn-success btn-sm font-weight-bold">
                <i class="fas fa-file-excel"></i> Export Laporan Order
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead style="background-color: #0056b3; color: white;">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Barang</th>
                            <th class="text-center">Stock Now</th>
                            <th>Rata-rata penggunaan / bulan & siapa penggunanya</th>
                            <th class="text-center">Kapan terakhir order</th>
                            <th class="text-center">Kebutuhan / Reorder</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderBarang as $index => $item)
                        <tr>
                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                            <td class="align-middle text-primary font-weight-bold">{{ $item->kode }} - {{ $item->nama_barang ?? $item->nama_material }}</td>
                            <td class="align-middle text-center font-weight-bold" style="font-size: 1.1rem; {{ $item->total_stok <= $item->min_stok ? 'color: #dc3545;' : 'color: #28a745;' }}">
                                {{ $item->total_stok ?? $item->stock_now }}
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold text-danger">{{ $item->rata_penggunaan }} {{ $item->satuan }}</span> / bulan
                                <br>
                                <span class="text-muted" style="font-size: 12px; font-weight: 600;">
                                    Pemakai: <span class="text-info">{{ $item->siapa_pengguna ?? 'Belum ada data' }}</span>
                                </span>
                            </td>
                            <td class="align-middle text-center text-muted">
                                {{ $item->terakhir_order ? \Carbon\Carbon::parse($item->terakhir_order)->format('d M Y') : 'Belum pernah order' }}
                            </td>
                            <td class="align-middle text-center">
                                @if($item->status_reorder == 'Perlu order')
                                    <span class="badge bg-danger text-white px-3 py-2" style="font-size: 12px;">Perlu Order</span>
                                @else
                                    <span class="badge bg-success text-white px-3 py-2" style="font-size: 12px;">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if($orderBarang->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data barang master.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
            <h5 class="m-0 font-weight-bold text-dark">2️⃣ Pemakaian Barang (Histori PR)</h5>
            <a href="{{ route('stationery.export.pemakaian') }}" class="btn btn-success btn-sm font-weight-bold">
                <i class="fas fa-file-excel"></i> Export Laporan Pemakaian
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead style="background-color: #dc3545; color: white;">
                        <tr>
                            <th class="text-center">No PR (FPB)</th>
                            <th class="text-center">Tanggal PR</th>
                            <th class="text-center">Kode Barang</th>
                            <th>Nama Material</th>
                            <th class="text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pemakaianBarang as $trx)
                        <tr>
                            <td class="align-middle text-center font-weight-bold text-secondary">{{ $trx->no_fpb ?? 'PR-' . $trx->id }}</td>
                            <td class="align-middle text-center text-muted">{{ \Carbon\Carbon::parse($trx->tanggal ?? $trx->tgl_input)->format('d-m-Y') }}</td>
                            <td class="align-middle text-center font-weight-bold text-primary">{{ $trx->stationery->kode ?? '-' }}</td>
                            <td class="align-middle">{{ $trx->stationery->nama_barang ?? '-' }}</td>
                            <td class="align-middle text-center font-weight-bold text-danger" style="font-size: 1.1rem;">
                                {{ $trx->jumlah }} <span style="font-size: 12px; font-weight: normal;" class="text-muted">{{ $trx->stationery->satuan ?? '' }}</span>
                            </td>
                        </tr>
                        @endforeach

                        @if($pemakaianBarang->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada histori pemakaian barang.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
