@php
    $locationsList = $attendance_locations ?? \App\Models\AttendanceLocation::orderBy('id', 'asc')->get();
    $activeCount = $locationsList->where('is_active', true)->count();
@endphp

<div id="tab-lokasi-absen" class="tab-content hidden w-full max-w-full flex-1 flex flex-col gap-6">

    {{-- KARTU UTAMA PENGATURAN LOKASI & RADIUS --}}
    <div class="glass-card rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col flex-1 min-h-0 bg-white w-full max-w-full">
        
        {{-- Header Section --}}
        <div class="px-5 sm:px-7 py-4 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                        <i class="fas fa-map-marked-alt text-sm text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">Pengaturan Zona & Batas Poligon Presensi (OSS)</h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Tentukan batas area presensi kantor langsung menggunakan pemetaan Poligon OSS interaktif</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-2xs ml-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span>{{ $activeCount }} Zona Aktif</span>
                    </span>
                </div>
            </div>

            {{-- Tombol Tambah Lokasi --}}
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" 
                        onclick="openAddLocationModal()" 
                        class="px-4 sm:px-5 rounded-xl text-white text-xs font-bold transition-all shadow-sm hover:opacity-95 active:scale-[0.98] flex items-center gap-2 cursor-pointer shrink-0" 
                        style="background-color: #004a99 !important; height: 38px;">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Zona dari Peta</span>
                </button>
            </div>
        </div>

        {{-- Interactive Map Preview Area --}}
        <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200/80">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-satellite text-blue-600"></i> Peta Visual Batas Area Poligon (OSS)
                </span>
                <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500 flex-wrap">
                    <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-indigo-500/70 border border-indigo-600"></span> Batas Zona Area Poligon (OSS)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Titik Pusat Pin</span>
                </div>
            </div>
            <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-200" style="height: 340px;">
                <div id="admin-geofence-map" class="w-full h-full z-10"></div>
                <div class="absolute bottom-3 right-3 z-20 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200/80 text-[11px] font-bold text-slate-700 shadow-sm flex items-center gap-3">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-mouse-pointer text-blue-600 text-[10px]"></i> Klik marker atau poligon untuk detail
                    </span>
                </div>
            </div>
        </div>

        {{-- Table List of Locations --}}
        <div class="overflow-y-auto flex-1 p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-600"></i> Daftar Zona Lokasi Presensi
                </h4>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="p-3.5 text-center w-12">No</th>
                            <th class="p-3.5">Nama Lokasi & Alamat</th>
                            <th class="p-3.5 text-center">Titik Koordinat</th>
                            <th class="p-3.5 text-center">Batas Zona Presensi (OSS)</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($locationsList as $idx => $loc)
                            @php
                                $hasPoly = !empty($loc->polygon) && is_array($loc->polygon) && count($loc->polygon) >= 3;
                                $polyCount = $hasPoly ? count($loc->polygon) : 0;
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                                        <i class="fas fa-building text-blue-600 text-xs"></i>
                                        <span>{{ $loc->nama_lokasi }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $loc->alamat ?: 'Tidak ada keterangan alamat' }}
                                    </div>
                                </td>
                                <td class="p-3.5 text-center font-mono">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold">
                                        <i class="fas fa-map-marker-alt text-blue-500 text-[10px]"></i>
                                        <span>{{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}</span>
                                    </div>
                                    <a href="https://www.google.com/maps?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="block text-[10px] text-blue-600 hover:underline mt-1 font-sans">
                                        Buka di Google Maps <i class="fas fa-external-link-alt text-[8px]"></i>
                                    </a>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($hasPoly)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-2xs">
                                            <i class="fas fa-draw-polygon text-indigo-500 text-xs"></i>
                                            <span>Area Poligon OSS ({{ $polyCount }} Titik)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fas fa-exclamation-triangle text-amber-500 text-[10px]"></i>
                                            <span>Belum Ada Poligon</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <form action="{{ route('attendance-locations.toggle', $loc->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="cursor-pointer transition-transform hover:scale-105" title="Klik untuk ubah status">
                                            @if($loc->is_active)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Tombol Fokus ke Peta --}}
                                        <button type="button" 
                                                onclick="focusLocationOnMap({{ $loc->latitude }}, {{ $loc->longitude }}, {{ $loc->radius_meter }}, '{{ addslashes($loc->nama_lokasi) }}', {{ json_encode($loc->polygon) }})" 
                                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors cursor-pointer" 
                                                title="Lihat di Peta">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>

                                        {{-- Tombol Edit --}}
                                        <button type="button" 
                                                onclick="openEditLocationModal({{ json_encode($loc) }})" 
                                                class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors cursor-pointer" 
                                                title="Edit Zona">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('attendance-locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus zona lokasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors cursor-pointer" 
                                                    title="Hapus Zona">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                    Belum ada zona lokasi presensi yang terdaftar. Silakan tambahkan lokasi kantor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL TAMBAH ZONA LOKASI (MEMILIH LANGSUNG DARI MAPS)   --}}
{{-- ======================================================= --}}
<div id="modalAddLocation" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-100 flex flex-col max-h-[94vh]">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
            <div>
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-blue-600"></i> Pilih Lokasi Presensi dari Maps
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Klik pada peta untuk menempatkan pin lokasi kantor tanpa perlu mengetik koordinat manual</p>
            </div>
            <button type="button" onclick="closeAddLocationModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('attendance-locations.store') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1">
            @csrf

            {{-- 1. Nama Lokasi --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lokasi / Area Kantor</label>
                <input type="text" name="nama_lokasi" id="add_nama_lokasi" placeholder="Contoh: Pabrik PT Indofood CBP Banjarbaru" required class="form-input-modern-clean">
            </div>

            {{-- 2. PENCARIAN & INTERACTIVE MAP (HERO SECTION) --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 uppercase flex items-center gap-1.5">
                        <i class="fas fa-crosshairs text-blue-600"></i> Pilih Titik Lokasi pada Peta
                    </label>
                    <span class="text-[11px] text-blue-700 font-bold bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                        <i class="fas fa-mouse-pointer text-[10px] mr-1"></i> Klik atau Geser Pin
                    </span>
                </div>

                {{-- Kotak Pencarian Tempat / Alamat --}}
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="add_search_map" placeholder="Cari nama tempat / jalan / kota (misal: PT Indofood CBP Banjarbaru)..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 transition shadow-2xs"
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); searchLocationAdd(); }">
                    </div>
                    <button type="button" id="add_btn_search" onclick="searchLocationAdd()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0">
                        <span>Cari</span>
                    </button>
                </div>

                {{-- Toolbar Gambar Poligon & GPS --}}
                <div class="flex items-center gap-2 flex-wrap pt-0.5">
                    <button type="button" onclick="startAddPolygonDraw()" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fas fa-draw-polygon text-indigo-600"></i>
                        <span>Gambar Poligon Area (OSS)</span>
                    </button>
                    <button type="button" onclick="clearAddPolygon()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold border border-slate-200 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fas fa-eraser text-slate-500"></i>
                        <span>Reset Poligon</span>
                    </button>
                    <button type="button" onclick="getCurrentLocationForAdd()" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition flex items-center gap-1.5 cursor-pointer ml-auto shadow-2xs">
                        <i class="fas fa-location-arrow text-blue-600"></i>
                        <span>Lokasi GPS Saya</span>
                    </button>
                </div>

                {{-- Map Container --}}
                <div class="relative rounded-2xl overflow-hidden border-2 border-blue-500/30 shadow-inner bg-slate-100" style="height: 290px;">
                    <div id="add-modal-map" class="w-full h-full z-10 cursor-crosshair"></div>
                    <div class="absolute top-2 left-2 z-20 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-700 shadow-sm flex items-center gap-1.5 pointer-events-none">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        <span>Klik peta untuk menaruh pin lokasi</span>
                    </div>
                </div>

                {{-- Status & Informasi Koordinat Otomatis (Tanpa Perlu Diketik) --}}
                <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-slate-600 font-bold">Titik Terpilih:</span>
                        <span class="font-mono font-extrabold text-blue-900 bg-white px-2 py-0.5 rounded border border-slate-200 text-[11px]" id="add_coords_indicator">
                            -3.548950, 114.729810
                        </span>
                    </div>
                    <div id="add_poly_status" class="font-bold text-[11px] text-slate-500">
                        Poligon: Belum ada area
                    </div>
                </div>
            </div>

            {{-- KOORDINAT LATITUDE & LONGITUDE DISIMPAN SECARA OTOMATIS (HIDDEN) --}}
            <input type="hidden" name="latitude" id="add_lat" value="-3.5489500" required>
            <input type="hidden" name="longitude" id="add_lng" value="114.7298100" required>
            <input type="hidden" name="polygon" id="add_polygon">
            <input type="hidden" name="radius_meter" id="add_radius_input" value="100">

            {{-- 3. Informasi Penentuan Batas Presensi (Poligon OSS) --}}
            <div class="p-3.5 bg-indigo-50/90 border border-indigo-200/90 rounded-2xl text-xs text-indigo-900 flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-draw-polygon text-xs"></i>
                </div>
                <div>
                    <b class="block font-extrabold text-indigo-900 mb-0.5">Batas Area Presensi Berbasis Poligon OSS</b>
                    <span class="text-[11px] text-indigo-700 leading-relaxed block">
                        Gunakan tombol <b>"Gambar Poligon Area (OSS)"</b> pada peta di atas untuk membentuk batasan wilayah kantor atau pabrik. Karyawan hanya dapat melakukan presensi apabila berada di dalam wilayah poligon tersebut.
                    </span>
                </div>
            </div>

            {{-- 4. Alamat (Terisi Otomatis saat Klik Peta) --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Lengkap (Terisi Otomatis dari Peta / Bisa Diubah)</label>
                <textarea name="alamat" id="add_alamat" rows="2" placeholder="Alamat akan terdeteksi otomatis saat Anda mengklik peta..." class="w-full p-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded cursor-pointer">
                <label for="add_is_active" class="text-xs font-bold text-slate-700 cursor-pointer">Aktifkan Zona Ini untuk Presensi</label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeAddLocationModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indofood-blue text-white text-xs font-bold hover:bg-blue-800 transition shadow-sm cursor-pointer" style="background-color: #004a99 !important;">
                    <i class="fas fa-save mr-1.5"></i> Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL EDIT ZONA LOKASI (MEMILIH LANGSUNG DARI MAPS)    --}}
{{-- ======================================================= --}}
<div id="modalEditLocation" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-100 flex flex-col max-h-[94vh]">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
            <div>
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fas fa-edit text-amber-600"></i> Edit Lokasi & Poligon Presensi
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Ubah titik lokasi langsung dengan mengklik peta atau menggeser pin</p>
            </div>
            <button type="button" onclick="closeEditLocationModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditLocation" action="" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lokasi / Area</label>
                <input type="text" name="nama_lokasi" id="edit_nama_lokasi" required class="form-input-modern-clean">
            </div>

            {{-- PENCARIAN & INTERACTIVE MAP EDIT --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 uppercase flex items-center gap-1.5">
                        <i class="fas fa-crosshairs text-amber-600"></i> Ubah Titik Lokasi pada Peta
                    </label>
                    <span class="text-[11px] text-amber-800 font-bold bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                        <i class="fas fa-mouse-pointer text-[10px] mr-1"></i> Klik atau Geser Pin
                    </span>
                </div>

                {{-- Kotak Pencarian Tempat Edit --}}
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="edit_search_map" placeholder="Cari tempat atau jalan baru..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500 transition shadow-2xs"
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); searchLocationEdit(); }">
                    </div>
                    <button type="button" id="edit_btn_search" onclick="searchLocationEdit()" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0">
                        <span>Cari</span>
                    </button>
                </div>

                {{-- Toolbar Poligon Edit --}}
                <div class="flex items-center gap-2 flex-wrap pt-0.5">
                    <button type="button" onclick="startEditPolygonDraw()" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fas fa-draw-polygon text-indigo-600"></i>
                        <span>Gambar Ulang Poligon</span>
                    </button>
                    <button type="button" onclick="clearEditPolygon()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold border border-slate-200 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fas fa-eraser text-slate-500"></i>
                        <span>Reset Poligon</span>
                    </button>
                    <button type="button" onclick="getCurrentLocationForEdit()" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200 transition flex items-center gap-1.5 cursor-pointer ml-auto shadow-2xs">
                        <i class="fas fa-location-arrow text-amber-600"></i>
                        <span>Lokasi GPS Saya</span>
                    </button>
                </div>

                {{-- Map Container Edit --}}
                <div class="relative rounded-2xl overflow-hidden border-2 border-amber-500/30 shadow-inner bg-slate-100" style="height: 290px;">
                    <div id="edit-modal-map" class="w-full h-full z-10 cursor-crosshair"></div>
                    <div class="absolute top-2 left-2 z-20 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-700 shadow-sm flex items-center gap-1.5 pointer-events-none">
                        <i class="fas fa-info-circle text-amber-600"></i>
                        <span>Klik peta untuk memindahkan pin lokasi</span>
                    </div>
                </div>

                {{-- Status & Info Koordinat Otomatis --}}
                <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-slate-600 font-bold">Titik Terpilih:</span>
                        <span class="font-mono font-extrabold text-amber-900 bg-white px-2 py-0.5 rounded border border-slate-200 text-[11px]" id="edit_coords_indicator">
                            -
                        </span>
                    </div>
                    <div id="edit_poly_status" class="font-bold text-[11px] text-slate-500">
                        Poligon: Belum ada area
                    </div>
                </div>
            </div>

            {{-- KOORDINAT DISIMPAN SECARA OTOMATIS (HIDDEN) --}}
            <input type="hidden" name="latitude" id="edit_lat" required>
            <input type="hidden" name="longitude" id="edit_lng" required>
            <input type="hidden" name="polygon" id="edit_polygon">
            <input type="hidden" name="radius_meter" id="edit_radius_input" value="100">

            {{-- 3. Informasi Penentuan Batas Presensi (Poligon OSS) --}}
            <div class="p-3.5 bg-indigo-50/90 border border-indigo-200/90 rounded-2xl text-xs text-indigo-900 flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-draw-polygon text-xs"></i>
                </div>
                <div>
                    <b class="block font-extrabold text-indigo-900 mb-0.5">Batas Area Presensi Berbasis Poligon OSS</b>
                    <span class="text-[11px] text-indigo-700 leading-relaxed block">
                        Gunakan tombol <b>"Edit / Gambar Poligon OSS"</b> untuk menyesuaikan titik sudut wilayah kantor. Karyawan hanya dapat presensi jika berada di dalam area poligon ini.
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat / Keterangan</label>
                <textarea name="alamat" id="edit_alamat" rows="2" class="w-full p-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-blue-600 rounded cursor-pointer">
                <label for="edit_is_active" class="text-xs font-bold text-slate-700 cursor-pointer">Aktifkan Zona Ini untuk Presensi</label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeEditLocationModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indofood-blue text-white text-xs font-bold hover:bg-blue-800 transition shadow-sm cursor-pointer" style="background-color: #004a99 !important;">
                    <i class="fas fa-check mr-1.5"></i> Perbarui Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let adminGeofenceMap = null;
    let adminMapLayers = [];
    const dbLocations = @json($locationsList);

    // =========================================================
    // 1. PETA UTAMA PREVIEW GEOFENCING & OSS POLIGON
    // =========================================================
    function initAdminGeofenceMap() {
        if (adminGeofenceMap) {
            adminGeofenceMap.invalidateSize();
            return;
        }

        const mapContainer = document.getElementById('admin-geofence-map');
        if (!mapContainer || typeof L === 'undefined') return;

        // Default center: Indofood Banjarbaru
        const defaultCenter = [-3.54895, 114.72981];
        adminGeofenceMap = L.map('admin-geofence-map').setView(defaultCenter, 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(adminGeofenceMap);

        renderLocationsOnAdminMap();
    }

    function renderLocationsOnAdminMap() {
        if (!adminGeofenceMap) return;

        adminMapLayers.forEach(layer => adminGeofenceMap.removeLayer(layer));
        adminMapLayers = [];

        const bounds = [];

        dbLocations.forEach(loc => {
            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);
            const radius = parseInt(loc.radius_meter) || 100;
            const isActive = Boolean(loc.is_active);

            if (isNaN(lat) || isNaN(lng)) return;

            const circleColor = isActive ? '#004a99' : '#94a3b8';
            const fillColor = isActive ? '#3b82f6' : '#cbd5e1';

            // 1. Gambar Poligon (jika ada)
            let polyLayer = null;
            let hasPoly = false;
            let polyCount = 0;
            if (loc.polygon) {
                let polyCoords = loc.polygon;
                if (typeof polyCoords === 'string') {
                    try { polyCoords = JSON.parse(polyCoords); } catch(e) {}
                }
                if (Array.isArray(polyCoords) && polyCoords.length >= 3) {
                    hasPoly = true;
                    polyCount = polyCoords.length;
                    polyLayer = L.polygon(polyCoords, {
                        color: isActive ? '#4f46e5' : '#94a3b8',
                        fillColor: isActive ? '#6366f1' : '#cbd5e1',
                        fillOpacity: 0.35,
                        weight: 2.5
                    }).addTo(adminGeofenceMap);

                    polyLayer.bindPopup(`
                        <div class="p-1 text-slate-800 font-sans">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-indigo-100 text-indigo-700 mb-1">
                                <i class="fas fa-draw-polygon"></i> Area Poligon (${polyCount} Titik)
                            </span>
                            <br><b class="text-sm text-blue-900">${loc.nama_lokasi}</b><br>
                            <span class="text-xs text-slate-500">${loc.alamat || ''}</span><br>
                            <small class="font-bold text-indigo-700">Radius Buffer: ${radius}m</small>
                        </div>
                    `);

                    adminMapLayers.push(polyLayer);
                    polyCoords.forEach(pt => bounds.push([pt[0], pt[1]]));
                }
            }

            // 2. Marker Pusat Pin Lokasi
            const marker = L.marker([lat, lng]).addTo(adminGeofenceMap);
            marker.bindPopup(`
                <div class="p-1 text-slate-800 font-sans">
                    <b class="text-sm text-blue-900">${loc.nama_lokasi}</b><br>
                    <span class="text-xs text-slate-500">${loc.alamat || ''}</span><br>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold ${isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
                        ${isActive ? 'Status: Aktif' : 'Status: Nonaktif'}
                    </span><br>
                    ${hasPoly ? `<small class="font-bold text-indigo-700"><i class="fas fa-draw-polygon mr-1"></i>Batas Area: Poligon OSS (${polyCount} Titik)</small>` : '<small class="text-amber-600 font-bold">Belum ada poligon OSS</small>'}
                </div>
            `);

            adminMapLayers.push(marker);
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            adminGeofenceMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 17 });
        }
    }

    function focusLocationOnMap(lat, lng, radius, name, polygon) {
        if (!adminGeofenceMap) return;

        let points = [[lat, lng]];
        if (polygon) {
            let polyCoords = polygon;
            if (typeof polyCoords === 'string') {
                try { polyCoords = JSON.parse(polyCoords); } catch(e) {}
            }
            if (Array.isArray(polyCoords) && polyCoords.length >= 3) {
                points = polyCoords.map(p => [p[0], p[1]]);
            }
        }

        if (points.length > 1) {
            adminGeofenceMap.fitBounds(points, { padding: [50, 50], maxZoom: 18 });
        } else {
            adminGeofenceMap.setView([lat, lng], 17);
        }

        L.popup()
            .setLatLng([lat, lng])
            .setContent(`<div class="font-bold text-xs text-blue-900">${name}</div><div class="text-[10px] text-indigo-700 font-bold"><i class="fas fa-draw-polygon mr-1"></i>Zona Area Poligon OSS</div>`)
            .openOn(adminGeofenceMap);

        document.getElementById('admin-geofence-map')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // =========================================================
    // 2. MODAL TAMBAH: MAP PICKER & REVERSE GEOCODING
    // =========================================================
    let addModalMap = null;
    let addCenterMarker = null;
    let addDrawnItems = null;
    let addDrawControl = null;
    let addPolygonDrawer = null;
    let isDrawingAddPolygon = false;

    function initAddModalMap() {
        const container = document.getElementById('add-modal-map');
        if (!container || typeof L === 'undefined') return;

        let curLat = parseFloat(document.getElementById('add_lat').value) || -3.54895;
        let curLng = parseFloat(document.getElementById('add_lng').value) || 114.72981;

        if (!addModalMap) {
            addModalMap = L.map('add-modal-map').setView([curLat, curLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(addModalMap);

            addDrawnItems = new L.FeatureGroup();
            addModalMap.addLayer(addDrawnItems);

            if (typeof L.Control.Draw !== 'undefined') {
                addDrawControl = new L.Control.Draw({
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            shapeOptions: { color: '#4f46e5', fillColor: '#6366f1', fillOpacity: 0.35, weight: 2.5 }
                        },
                        polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false
                    },
                    edit: { featureGroup: addDrawnItems, remove: true }
                });
                addModalMap.addControl(addDrawControl);

                addModalMap.on(L.Draw.Event.CREATED, function (e) {
                    isDrawingAddPolygon = false;
                    addDrawnItems.clearLayers();
                    const layer = e.layer;
                    addDrawnItems.addLayer(layer);
                    syncAddPolygonFromLayer(layer);
                });

                addModalMap.on(L.Draw.Event.EDITED, function (e) {
                    addDrawnItems.eachLayer(layer => syncAddPolygonFromLayer(layer));
                });

                addModalMap.on(L.Draw.Event.DELETED, function (e) {
                    clearAddPolygon();
                });

                addModalMap.on(L.Draw.Event.DRAWSTART, function() { isDrawingAddPolygon = true; });
                addModalMap.on(L.Draw.Event.DRAWSTOP, function() { isDrawingAddPolygon = false; });
            }

            // KLIK PADA PETA UNTUK MEMILIH LOKASI
            addModalMap.on('click', function(e) {
                if (isDrawingAddPolygon) return;
                setAddCenterPoint(e.latlng.lat, e.latlng.lng, false);
                reverseGeocodeAdd(e.latlng.lat, e.latlng.lng);
            });
        }

        setAddCenterPoint(curLat, curLng, false);
        setTimeout(() => addModalMap.invalidateSize(), 150);
    }

    function setAddCenterPoint(lat, lng, pan = true) {
        if (!addModalMap) return;

        document.getElementById('add_lat').value = lat.toFixed(7);
        document.getElementById('add_lng').value = lng.toFixed(7);
        document.getElementById('add_coords_indicator').innerText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        if (!addCenterMarker) {
            addCenterMarker = L.marker([lat, lng], { draggable: true }).addTo(addModalMap);
            addCenterMarker.on('drag', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('add_lat').value = pos.lat.toFixed(7);
                document.getElementById('add_lng').value = pos.lng.toFixed(7);
                document.getElementById('add_coords_indicator').innerText = `${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
            });
            addCenterMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                reverseGeocodeAdd(pos.lat, pos.lng);
            });
        } else {
            addCenterMarker.setLatLng([lat, lng]);
        }

        if (pan) {
            addModalMap.panTo([lat, lng]);
        }
    }

    // Pencarian Lokasi Modal Tambah
    async function searchLocationAdd() {
        const query = document.getElementById('add_search_map').value.trim();
        if (!query) return;

        const btn = document.getElementById('add_btn_search');
        const origText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`, {
                headers: { 'Accept-Language': 'id,en' }
            });
            const data = await res.json();
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                setAddCenterPoint(lat, lng, true);
                addModalMap.setView([lat, lng], 17);

                const alamatEl = document.getElementById('add_alamat');
                if (alamatEl && !alamatEl.value) {
                    alamatEl.value = data[0].display_name;
                }
                const namaEl = document.getElementById('add_nama_lokasi');
                if (namaEl && !namaEl.value) {
                    namaEl.value = query;
                }
            } else {
                Swal.fire('Lokasi Tidak Ditemukan', 'Silakan coba kata kunci jalan atau area lain.', 'info');
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }

    // Deteksi Alamat Otomatis saat Klik Peta
    function reverseGeocodeAdd(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
            headers: { 'Accept-Language': 'id,en' }
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.display_name) {
                const alamatEl = document.getElementById('add_alamat');
                if (alamatEl && (!alamatEl.value || alamatEl.value.includes('akan terdeteksi'))) {
                    alamatEl.value = data.display_name;
                }
            }
        })
        .catch(() => {});
    }

    function syncAddPolygonFromLayer(layer) {
        if (!layer || !layer.getLatLngs) return;
        const latlngs = layer.getLatLngs()[0];
        const points = latlngs.map(pt => [parseFloat(pt.lat.toFixed(7)), parseFloat(pt.lng.toFixed(7))]);
        document.getElementById('add_polygon').value = JSON.stringify(points);
        document.getElementById('add_poly_status').innerHTML = `<span class="text-indigo-700 font-black"><i class="fas fa-check-circle"></i> Poligon Aktif (${points.length} Titik Sudut)</span>`;
    }

    function startAddPolygonDraw() {
        if (!addModalMap || typeof L.Draw === 'undefined') return;
        if (addPolygonDrawer) addPolygonDrawer.disable();

        addPolygonDrawer = new L.Draw.Polygon(addModalMap, {
            allowIntersection: false,
            shapeOptions: { color: '#4f46e5', fillColor: '#6366f1', fillOpacity: 0.35, weight: 2.5 }
        });
        addPolygonDrawer.enable();
        isDrawingAddPolygon = true;
    }

    function clearAddPolygon() {
        if (addDrawnItems) addDrawnItems.clearLayers();
        if (addPolygonDrawer) addPolygonDrawer.disable();
        isDrawingAddPolygon = false;
        document.getElementById('add_polygon').value = '';
        document.getElementById('add_poly_status').innerHTML = `Poligon: Belum ada area`;
    }

    function openAddLocationModal() {
        document.getElementById('modalAddLocation')?.classList.remove('hidden');
        setTimeout(initAddModalMap, 200);
    }

    function closeAddLocationModal() {
        document.getElementById('modalAddLocation')?.classList.add('hidden');
    }

    function updateAddRadius(val) {
        const input = document.getElementById('add_radius_input');
        if (input) input.value = val;
    }

    function getCurrentLocationForAdd() {
        if (!navigator.geolocation) {
            Swal.fire('GPS Tidak Didukung', 'Browser Anda tidak mendukung geolokasi GPS.', 'warning');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                setAddCenterPoint(lat, lng, true);
                addModalMap.setView([lat, lng], 17);
                reverseGeocodeAdd(lat, lng);
                Swal.fire({
                    icon: 'success',
                    title: 'Lokasi Anda Dipilih!',
                    text: `Akurasi GPS: ±${Math.round(pos.coords.accuracy)}m`,
                    timer: 1800,
                    showConfirmButton: false
                });
            },
            err => {
                Swal.fire('Gagal Mengambil GPS', 'Pastikan izin lokasi telah diaktifkan di peramban Anda.', 'error');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // =========================================================
    // 3. MODAL EDIT: MAP PICKER & REVERSE GEOCODING
    // =========================================================
    let editModalMap = null;
    let editCenterMarker = null;
    let editDrawnItems = null;
    let editDrawControl = null;
    let editPolygonDrawer = null;
    let isDrawingEditPolygon = false;
    let currentEditingLoc = null;

    function initEditModalMap() {
        const container = document.getElementById('edit-modal-map');
        if (!container || typeof L === 'undefined') return;

        let curLat = parseFloat(document.getElementById('edit_lat').value) || -3.54895;
        let curLng = parseFloat(document.getElementById('edit_lng').value) || 114.72981;

        if (!editModalMap) {
            editModalMap = L.map('edit-modal-map').setView([curLat, curLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(editModalMap);

            editDrawnItems = new L.FeatureGroup();
            editModalMap.addLayer(editDrawnItems);

            if (typeof L.Control.Draw !== 'undefined') {
                editDrawControl = new L.Control.Draw({
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            shapeOptions: { color: '#4f46e5', fillColor: '#6366f1', fillOpacity: 0.35, weight: 2.5 }
                        },
                        polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false
                    },
                    edit: { featureGroup: editDrawnItems, remove: true }
                });
                editModalMap.addControl(editDrawControl);

                editModalMap.on(L.Draw.Event.CREATED, function (e) {
                    isDrawingEditPolygon = false;
                    editDrawnItems.clearLayers();
                    const layer = e.layer;
                    editDrawnItems.addLayer(layer);
                    syncEditPolygonFromLayer(layer);
                });

                editModalMap.on(L.Draw.Event.EDITED, function (e) {
                    editDrawnItems.eachLayer(layer => syncEditPolygonFromLayer(layer));
                });

                editModalMap.on(L.Draw.Event.DELETED, function (e) {
                    clearEditPolygon();
                });

                editModalMap.on(L.Draw.Event.DRAWSTART, function() { isDrawingEditPolygon = true; });
                editModalMap.on(L.Draw.Event.DRAWSTOP, function() { isDrawingEditPolygon = false; });
            }

            // KLIK PETA UNTUK MEMILIH LOKASI PADA EDIT
            editModalMap.on('click', function(e) {
                if (isDrawingEditPolygon) return;
                setEditCenterPoint(e.latlng.lat, e.latlng.lng, false);
                reverseGeocodeEdit(e.latlng.lat, e.latlng.lng);
            });
        }

        // Render poligon tersimpan jika ada
        editDrawnItems.clearLayers();
        let polyData = currentEditingLoc ? currentEditingLoc.polygon : null;
        if (polyData) {
            if (typeof polyData === 'string') {
                try { polyData = JSON.parse(polyData); } catch(e) {}
            }
            if (Array.isArray(polyData) && polyData.length >= 3) {
                const poly = L.polygon(polyData, {
                    color: '#4f46e5',
                    fillColor: '#6366f1',
                    fillOpacity: 0.35,
                    weight: 2.5
                });
                editDrawnItems.addLayer(poly);
                syncEditPolygonFromLayer(poly);
            } else {
                clearEditPolygon();
            }
        } else {
            clearEditPolygon();
        }

        setEditCenterPoint(curLat, curLng, false);
        setTimeout(() => editModalMap.invalidateSize(), 150);
    }

    function setEditCenterPoint(lat, lng, pan = true) {
        if (!editModalMap) return;

        document.getElementById('edit_lat').value = lat.toFixed(7);
        document.getElementById('edit_lng').value = lng.toFixed(7);
        document.getElementById('edit_coords_indicator').innerText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        if (!editCenterMarker) {
            editCenterMarker = L.marker([lat, lng], { draggable: true }).addTo(editModalMap);
            editCenterMarker.on('drag', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('edit_lat').value = pos.lat.toFixed(7);
                document.getElementById('edit_lng').value = pos.lng.toFixed(7);
                document.getElementById('edit_coords_indicator').innerText = `${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
            });
            editCenterMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                reverseGeocodeEdit(pos.lat, pos.lng);
            });
        } else {
            editCenterMarker.setLatLng([lat, lng]);
        }

        if (pan) {
            editModalMap.panTo([lat, lng]);
        }
    }

    // Pencarian Lokasi Modal Edit
    async function searchLocationEdit() {
        const query = document.getElementById('edit_search_map').value.trim();
        if (!query) return;

        const btn = document.getElementById('edit_btn_search');
        const origText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`, {
                headers: { 'Accept-Language': 'id,en' }
            });
            const data = await res.json();
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                setEditCenterPoint(lat, lng, true);
                editModalMap.setView([lat, lng], 17);

                const alamatEl = document.getElementById('edit_alamat');
                if (alamatEl) {
                    alamatEl.value = data[0].display_name;
                }
            } else {
                Swal.fire('Lokasi Tidak Ditemukan', 'Silakan coba kata kunci jalan atau kota lain.', 'info');
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }

    function reverseGeocodeEdit(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
            headers: { 'Accept-Language': 'id,en' }
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.display_name) {
                const alamatEl = document.getElementById('edit_alamat');
                if (alamatEl) {
                    alamatEl.value = data.display_name;
                }
            }
        })
        .catch(() => {});
    }

    function syncEditPolygonFromLayer(layer) {
        if (!layer || !layer.getLatLngs) return;
        const latlngs = layer.getLatLngs()[0];
        const points = latlngs.map(pt => [parseFloat(pt.lat.toFixed(7)), parseFloat(pt.lng.toFixed(7))]);
        document.getElementById('edit_polygon').value = JSON.stringify(points);
        document.getElementById('edit_poly_status').innerHTML = `<span class="text-indigo-700 font-black"><i class="fas fa-check-circle"></i> Poligon Aktif (${points.length} Titik Sudut)</span>`;
    }

    function startEditPolygonDraw() {
        if (!editModalMap || typeof L.Draw === 'undefined') return;
        if (editPolygonDrawer) editPolygonDrawer.disable();

        editPolygonDrawer = new L.Draw.Polygon(editModalMap, {
            allowIntersection: false,
            shapeOptions: { color: '#4f46e5', fillColor: '#6366f1', fillOpacity: 0.35, weight: 2.5 }
        });
        editPolygonDrawer.enable();
        isDrawingEditPolygon = true;
    }

    function clearEditPolygon() {
        if (editDrawnItems) editDrawnItems.clearLayers();
        if (editPolygonDrawer) editPolygonDrawer.disable();
        isDrawingEditPolygon = false;
        document.getElementById('edit_polygon').value = '';
        document.getElementById('edit_poly_status').innerHTML = `Poligon: Belum ada area`;
    }

    function openEditLocationModal(loc) {
        currentEditingLoc = loc;
        document.getElementById('formEditLocation').action = `/attendance-locations/${loc.id}`;
        document.getElementById('edit_nama_lokasi').value = loc.nama_lokasi;
        document.getElementById('edit_lat').value = loc.latitude;
        document.getElementById('edit_lng').value = loc.longitude;
        document.getElementById('edit_alamat').value = loc.alamat || '';
        document.getElementById('edit_is_active').checked = Boolean(loc.is_active);

        const radInput = document.getElementById('edit_radius_input');
        if (radInput) radInput.value = loc.radius_meter || 100;

        document.getElementById('modalEditLocation')?.classList.remove('hidden');
        setTimeout(initEditModalMap, 200);
    }

    function closeEditLocationModal() {
        document.getElementById('modalEditLocation')?.classList.add('hidden');
        currentEditingLoc = null;
    }

    function updateEditRadius(val) {
        const input = document.getElementById('edit_radius_input');
        if (input) input.value = val;
    }

    function getCurrentLocationForEdit() {
        if (!navigator.geolocation) {
            Swal.fire('GPS Tidak Didukung', 'Browser Anda tidak mendukung geolokasi GPS.', 'warning');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                setEditCenterPoint(lat, lng, true);
                editModalMap.setView([lat, lng], 17);
                reverseGeocodeEdit(lat, lng);
                Swal.fire({
                    icon: 'success',
                    title: 'Lokasi Anda Dipilih!',
                    text: `Akurasi GPS: ±${Math.round(pos.coords.accuracy)}m`,
                    timer: 1800,
                    showConfirmButton: false
                });
            },
            err => {
                Swal.fire('Gagal Mengambil GPS', 'Pastikan izin lokasi telah diaktifkan di peramban Anda.', 'error');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // =========================================================
    // 4. TAB EVENT OBSERVER UNTUK INISIALISASI PETA
    // =========================================================
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new MutationObserver(() => {
            const tabEl = document.getElementById('tab-lokasi-absen');
            if (tabEl && !tabEl.classList.contains('hidden')) {
                setTimeout(initAdminGeofenceMap, 200);
            }
        });

        const target = document.getElementById('tab-lokasi-absen');
        if (target) {
            observer.observe(target, { attributes: true, attributeFilter: ['class'] });
        }
    });
</script>
