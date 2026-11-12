@extends('layouts.app') {{-- Sesuaikan dengan nama file layout utama kamu --}}

@section('content')
<div class="container mt-4">
    <h2>Form Transaksi Keluar/Masuk Barang</h2>

    {{-- Menampilkan pesan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Menampilkan pesan sukses dari Controller --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            {{-- Pastikan action mengarah ke route penyimpanannya --}}
            <form action="{{ url('/stationery-transaction') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stationery_id" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <select name="stationery_id" id="stationery_id" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            {{-- Looping data barang dari database --}}
                            @foreach ($stationeries as $item)
                                <option value="{{ $item->id }}" {{ old('stationery_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} {{-- Ganti 'name' sesuai nama kolom di tabel stationeries --}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="masuk" {{ old('type') == 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                            <option value="keluar" {{ old('type') == 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ old('jumlah') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="dept" class="form-label">Departemen</label>
                        <input type="text" name="dept" id="dept" class="form-control" value="{{ old('dept') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="vendor" class="form-label">Vendor</label>
                        <input type="text" name="vendor" id="vendor" class="form-control" value="{{ old('vendor') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="no_fpb" class="form-label">No. FPB</label>
                        <input type="text" name="no_fpb" id="no_fpb" class="form-control" value="{{ old('no_fpb') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="no_dokumen" class="form-label">No. Dokumen</label>
                        <input type="text" name="no_dokumen" id="no_dokumen" class="form-control" value="{{ old('no_dokumen') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                <a href="{{ url('/dashboard#stationery') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
