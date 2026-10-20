@php
    $loggedUser = Auth::user();
    $boundEmp = null;
    if ($loggedUser && $loggedUser->nik) {
        $boundEmp = $loggedUser->employee ?? \App\Models\Employee::where('nik', $loggedUser->nik)->first();
    }
    $isKaryawanRole = ($loggedUser && $loggedUser->role === 'karyawan');
    $hasBoundNik = !empty($loggedUser->nik) && !empty($boundEmp);
@endphp

{{-- Mengubah items-center menjadi items-start dan mengatur jarak atas (pt-6) --}}
<div class="flex items-start justify-center min-h-screen w-full bg-transparent px-2.5 sm:px-4 pt-2 sm:pt-6 pb-12">
    <div class="glass-card w-full max-w-5xl p-4 sm:p-8 lg:p-10 shadow-2xl relative border-t-8 border-indofood-blue flex flex-col mx-auto bg-white rounded-2xl sm:rounded-3xl">

        {{-- Header Form Absen --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 border-b border-slate-100 pb-5 gap-3">
            <div class="flex items-center gap-3.5 sm:gap-4">
                <div class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-blue-50 text-indofood-blue shadow-inner border border-blue-100 shrink-0">
                    <i class="fas fa-fingerprint text-xl sm:text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-wide uppercase leading-tight">Portal Kiosk Absensi</h2>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mt-0.5 sm:mt-1" id="live-clock-absen">Memuat waktu...</p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-indofood-blue border border-blue-100">
                    <i class="fas fa-satellite text-[10px] text-blue-500 animate-pulse"></i> GPS & Face Verified
                </span>
            </div>
        </div>

        {{-- KARTU PROFIL KARYAWAN ELEGAN & PROFESIONAL --}}
        @if($isKaryawanRole)
            @if($hasBoundNik)
                <div class="mb-6 p-4 sm:p-5 rounded-2xl shadow-lg relative overflow-hidden" 
                     style="background: linear-gradient(135deg, #0b192c 0%, #1e3a8a 50%, #0f172a 100%) !important; border: 1px solid #1e3a8a !important; color: #ffffff !important;">
                    {{-- Soft ambient decoration --}}
                    <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full pointer-events-none" style="background: rgba(59, 130, 246, 0.15); filter: blur(30px);"></div>
                    <div class="absolute left-1/4 -top-8 w-36 h-36 rounded-full pointer-events-none" style="background: rgba(56, 189, 248, 0.15); filter: blur(30px);"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 sm:gap-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center font-black text-lg sm:text-xl shadow-md shrink-0"
                                 style="background: linear-gradient(135deg, #2563eb, #38bdf8); color: #ffffff; border: 2px solid rgba(255,255,255,0.2);">
                                <span>{{ strtoupper(substr($boundEmp->nama, 0, 2)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-base sm:text-lg font-black tracking-wide truncate uppercase" style="color: #ffffff !important; margin: 0;">{{ $boundEmp->nama }}</h3>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black"
                                          style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid rgba(147, 197, 253, 0.3);">
                                        <i class="fas fa-id-badge text-[10px]" style="color: #38bdf8;"></i> NIK: {{ $loggedUser->nik }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium" style="color: #cbd5e1;">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-briefcase text-[11px]" style="color: #38bdf8;"></i>
                                        <span class="font-bold" style="color: #f8fafc;">{{ $boundEmp->jabatan ?? '-' }}</span>
                                    </span>
                                    <span class="hidden sm:inline" style="color: #64748b;">•</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-[11px]" style="color: #60a5fa;"></i>
                                        <span>Regu <b style="color: #f8fafc;">{{ $boundEmp->regu ?? '-' }}</b></span>
                                    </span>
                                    <span class="hidden sm:inline" style="color: #64748b;">•</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-building text-[11px]" style="color: #818cf8;"></i>
                                        <span>Unit <b style="color: #f8fafc;">{{ $boundEmp->org_unit_text ?? '-' }}</b></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center self-start md:self-auto shrink-0 pt-2 md:pt-0 border-t md:border-0 w-full md:w-auto justify-between md:justify-end" style="border-color: rgba(255,255,255,0.1);">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm"
                                  style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(52, 211, 153, 0.4); color: #6ee7b7;">
                                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #34d399;"></span>
                                <span>Akun Terverifikasi</span>
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold shadow-sm shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-amber-900 uppercase tracking-wide">AKUN BELUM MEMILIKI NIK KARYAWAN</h4>
                        <p class="text-xs font-medium text-amber-700 mt-0.5">
                            Silakan hubungi <b>Administrator / HRD</b> untuk menautkan profil akun Anda ke NIK karyawan agar dapat melakukan absensi.
                        </p>
                    </div>
                </div>
            @endif
        @endif

        {{-- Form Layout --}}
        <form action="{{ route('attendance.store') }}" method="POST" id="form-absen-mandiri" class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            @csrf
            <input type="hidden" name="action" value="absen_mandiri">
            <input type="hidden" name="latitude" id="latitude" required>
            <input type="hidden" name="longitude" id="longitude" required>
            <input type="hidden" name="lokasi_absen" id="lokasi_absen">

            {{-- BAGIAN KIRI: Form Isian --}}
            <div class="lg:col-span-7 space-y-4 sm:space-y-5">
                <div>
                    <label class="text-[11px] font-black text-slate-500 uppercase ml-1 block mb-1.5">NIK Karyawan</label>
                    <input type="text" name="id_karyawan" id="id_karyawan" 
                           value="{{ $isKaryawanRole && $hasBoundNik ? $loggedUser->nik : '' }}"
                           {{ $isKaryawanRole && $hasBoundNik ? 'readonly' : '' }}
                           placeholder="Contoh: 80001234" required 
                           class="form-input-modern w-full text-slate-900 border border-slate-200 {{ $isKaryawanRole && $hasBoundNik ? 'bg-slate-100 font-bold cursor-not-allowed select-none' : 'bg-slate-50 focus:bg-white focus:ring-indofood-blue' }} rounded-xl shadow-xs py-3 px-4 text-sm transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" 
                               value="{{ $isKaryawanRole && $hasBoundNik ? $boundEmp->nama : '' }}"
                               placeholder="Ketik nama..." readonly required 
                               class="form-input-modern w-full text-slate-900 border border-slate-200 {{ $isKaryawanRole && $hasBoundNik ? 'bg-slate-100/90 font-bold' : 'bg-slate-50' }} rounded-xl shadow-xs py-3 px-4 text-sm transition-all pointer-events-none select-none">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Posisi / Jabatan</label>
                        <input type="text" name="posisi" id="posisi" 
                               value="{{ $isKaryawanRole && $hasBoundNik ? $boundEmp->jabatan : '' }}"
                               placeholder="Ketik posisi..." readonly required 
                               class="form-input-modern w-full text-slate-900 border border-slate-200 {{ $isKaryawanRole && $hasBoundNik ? 'bg-slate-100/90 font-bold' : 'bg-slate-50' }} rounded-xl shadow-xs py-3 px-4 text-sm transition-all pointer-events-none select-none">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Pilih Tindakan</label>
                    <select name="status_kehadiran" class="form-input-modern w-full text-slate-900 cursor-pointer border border-slate-200 bg-slate-50 focus:bg-white focus:ring-indofood-blue rounded-xl font-bold py-3.5 px-4 text-sm transition-all" required>
                        <option value="" disabled selected>-- Tekan untuk memilih --</option>
                        <option value="Hadir">🟢 Check-In (Masuk Kerja)</option>
                        <option value="Pulang">🔴 Check-Out (Pulang Kerja)</option>
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Terlambat karena macet..." class="form-input-modern w-full text-slate-900 border border-slate-200 bg-slate-50 focus:bg-white focus:ring-indofood-blue rounded-xl shadow-sm py-3 px-4 text-sm transition-all">
                </div>
            </div>

            {{-- BAGIAN KANAN: Kamera & Tracking GPS --}}
            <div class="lg:col-span-5 flex flex-col gap-5">
                <div class="flex flex-col items-center bg-slate-50 p-5 rounded-3xl border border-slate-100 h-full shadow-inner justify-center">
                    <label class="text-[11px] font-black text-slate-500 uppercase mb-4 text-center leading-tight">Area Pemindaian Wajah <br> <span class="text-indofood-blue">(Wajib Terlihat)</span></label>

                    <div class="relative bg-white rounded-2xl border-4 border-slate-200 overflow-hidden shadow-sm flex items-center justify-center w-56 h-56 mx-auto mb-2">
                        <div id="camera-loading" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 z-0">
                            <i class="fas fa-camera text-3xl mb-2 animate-pulse text-blue-300"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Memuat Kamera...</p>
                        </div>
                        <video id="camera-stream" autoplay playsinline class="absolute inset-0 w-full h-full object-cover z-10 hidden transform scale-x-[-1]"></video>
                        <canvas id="camera-canvas" class="hidden"></canvas>
                    </div>
                    <input type="hidden" name="foto_absen" id="foto_absen" required>
                </div>

                @if($isKaryawanRole && !$hasBoundNik)
                    <button type="button" disabled class="w-full flex items-center justify-center gap-2 bg-slate-300 text-slate-500 font-extrabold py-4 rounded-xl uppercase text-sm tracking-widest cursor-not-allowed shrink-0">
                        <i class="fas fa-ban text-lg"></i>
                        <span>AKUN BELUM MEMILIKI NIK</span>
                    </button>
                @else
                    <button type="submit" id="btn-submit-absen" class="w-full flex items-center justify-center gap-2 bg-indofood-blue text-white font-extrabold py-4 rounded-xl uppercase text-sm tracking-widest shadow-lg hover:bg-blue-800 transition-all transform hover:-translate-y-0.5 shrink-0">
                        <i class="fas fa-shield-alt text-lg"></i>
                        <span>SIMPAN ABSENSI</span>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- ALERT BERHASIL DARI CONTROLLER DITAMBAHKAN DI SINI               --}}
{{-- ================================================================ --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'BERHASIL!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#004a99',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

<script>
    let lastValidCoordinates = {
        lat: null,
        lng: null,
        accuracy: null,
        timestamp: null,
        zoneInfo: null
    };

    const ZONES = [
        {
            name: "Area Gas",
            center_lat: -3.549192,
            center_lng: 114.730445,
            polygon: [
                [-3.549134, 114.730352],
                [-3.549073, 114.730467],
                [-3.549254, 114.730538],
                [-3.549311, 114.730453]
            ],
            bounds: {
                north: -3.549073, south: -3.549311, east: 114.730538, west: 114.730352
            },
            allowed_positions: []
        },
        {
            name: "Ruangan Produksi",
            center_lat: -3.549233,
            center_lng: 114.730057,
            polygon: [
                [-3.549122, 114.730147],
                [-3.549250, 114.730196],
                [-3.549368, 114.730001],
                [-3.549192, 114.729886]
            ],
            bounds: {
                north: -3.549122, south: -3.549368, east: 114.730196, west: 114.729886
            },
            allowed_positions: []
        },
        {
            name: "Pos Security",
            center_lat: -3.550000,
            center_lng: 114.731213,
            polygon: [
                [-3.549905, 114.731293],
                [-3.549991, 114.731098],
                [-3.550117, 114.731111],
                [-3.549987, 114.731349]
            ],
            bounds: {
                north: -3.549905, south: -3.550117, east: 114.731349, west: 114.731098
            },
            allowed_positions: []
        },
        {
            name: "Area Warehouse",
            center_lat: -3.550112,
            center_lng: 114.730494,
            polygon: [
                [-3.549961, 114.730549],
                [-3.550069, 114.730343],
                [-3.550265, 114.730430],
                [-3.550152, 114.730652]
            ],
            bounds: {
                north: -3.549961, south: -3.550265, east: 114.730652, west: 114.730343
            },
            allowed_positions: []
        }
    ];

    const MAX_RADIUS_METERS = 100;
    let currentWatchId = null;

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const p1 = lat1 * Math.PI/180;
        const p2 = lat2 * Math.PI/180;
        const deltaP = (lat2-lat1) * Math.PI/180;
        const deltaLon = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(deltaP/2) * Math.sin(deltaP/2) + Math.cos(p1) * Math.cos(p2) * Math.sin(deltaLon/2) * Math.sin(deltaLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function isWithinZoneBounds(userLat, userLng, zone) {
        if (zone.polygon) {
            let x = userLat, y = userLng;
            let inside = false;
            for (let i = 0, j = zone.polygon.length - 1; i < zone.polygon.length; j = i++) {
                let xi = zone.polygon[i][0], yi = zone.polygon[i][1];
                let xj = zone.polygon[j][0], yj = zone.polygon[j][1];
                let intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }
        const lat_ok = userLat >= zone.bounds.south && userLat <= zone.bounds.north;
        const lng_ok = userLng >= zone.bounds.west && userLng <= zone.bounds.east;
        return lat_ok && lng_ok;
    }

    function isWithinZoneRadius(userLat, userLng, zone) {
        const distFromCenter = getDistance(userLat, userLng, zone.center_lat, zone.center_lng);
        return distFromCenter <= MAX_RADIUS_METERS;
    }

    function getClosestZoneWithDetails(userLat, userLng) {
        let closestZone = null;
        let distFromClosest = Infinity;
        let isInBounds = false;
        let isInRadius = false;

        for (const zone of ZONES) {
            const inBounds = isWithinZoneBounds(userLat, userLng, zone);
            const inRadius = isWithinZoneRadius(userLat, userLng, zone);
            const distFromCenter = getDistance(userLat, userLng, zone.center_lat, zone.center_lng);

            if (inBounds) {
                if (!closestZone || !isInBounds || distFromCenter < distFromClosest) {
                    closestZone = zone; distFromClosest = distFromCenter;
                    isInBounds = true; isInRadius = true;
                }
            } else if (inRadius) {
                if (!closestZone || (isInBounds === false && (!isInRadius || distFromCenter < distFromClosest))) {
                    closestZone = zone; distFromClosest = distFromCenter;
                    isInRadius = true;
                }
            } else {
                if (!closestZone || (isInBounds === false && isInRadius === false && distFromCenter < distFromClosest)) {
                    closestZone = zone; distFromClosest = distFromCenter;
                }
            }
        }
        return { zone: closestZone, distance: distFromClosest, inBounds: isInBounds, inRadius: isInRadius };
    }

    function updateClockAbsen() {
        const now = new Date();
        const options = { timeZone: 'Asia/Makassar', weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        const label = document.getElementById('live-clock-absen');
        if(label) label.innerText = new Intl.DateTimeFormat('id-ID', options).format(now) + " WITA";
    }
    setInterval(updateClockAbsen, 1000); updateClockAbsen();

    function startGPSTracking(forceRefresh = false) {
        const liveGpsText = document.getElementById('live-gps-text');

        if (!navigator.geolocation) {
            if(liveGpsText) liveGpsText.innerHTML = '<span class="text-red-500">Browser tidak mendukung GPS!</span>';
            return;
        }

        if (forceRefresh && liveGpsText) {
            liveGpsText.innerHTML = 'Mencari Satelit Baru... <i class="fas fa-spinner fa-spin"></i>';
            if (currentWatchId) { navigator.geolocation.clearWatch(currentWatchId); }
        }

        currentWatchId = navigator.geolocation.watchPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                lastValidCoordinates.lat = lat;
                lastValidCoordinates.lng = lng;
                lastValidCoordinates.accuracy = accuracy;
                lastValidCoordinates.timestamp = Date.now();
                lastValidCoordinates.zoneInfo = getClosestZoneWithDetails(lat, lng);

                const zoneInfo = lastValidCoordinates.zoneInfo;
                const zone = zoneInfo.zone;
                const distance = zoneInfo.distance;

                if (liveGpsText) {
                    if (!zone) {
                        liveGpsText.innerHTML = `
                            <div class="text-slate-600 text-[11px] mb-1">📍 ${lat.toFixed(6)}, ${lng.toFixed(6)} | Akurasi: ±${Math.round(accuracy)}m</div>
                            <div class="text-red-500 text-xs font-bold">Tidak ada zona yang terdeteksi</div>
                        `;
                        return;
                    }

                    let statusIcon = '🔴'; let statusColor = 'text-red-500'; let statusText = '(Di Luar Area)';
                    if (zoneInfo.inBounds) { statusIcon = '🟢'; statusColor = 'text-green-600'; statusText = `(Siap Absen - Di Dalam ${zone.name})`; }
                    else if (zoneInfo.inRadius) { statusIcon = '🟡'; statusColor = 'text-yellow-600'; statusText = '(Toleransi Akurasi)'; }

                    liveGpsText.innerHTML = `
                        <div class="text-slate-600 text-[10px] mb-1">📍 ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                        <div class="text-[10px] mb-1 text-slate-500">Radius Alat: <b class="${accuracy > 50 ? 'text-red-500' : 'text-green-600'}">${Math.round(accuracy)} meter</b></div>
                        <div class="text-[11px]">Area: <b>${zone.name}</b></div>
                        <div class="text-sm font-extrabold ${statusColor}">${statusIcon} Jarak ke pusat: ${Math.round(distance)}m ${statusText}</div>
                    `;
                }
            },
            function(err) {
                let errorMsg = '<span class="text-red-500 font-bold">🔴 Akses GPS Ditolak / Mati!</span>';
                if(err.code === 1) errorMsg = '<span class="text-red-500 font-bold">🔴 Izinkan Lokasi di Browser!</span>';
                if(err.code === 2) errorMsg = '<span class="text-red-500 font-bold">🔴 Nyalakan GPS di HP/PC!</span>';
                if(err.code === 3 && !lastValidCoordinates.lat) errorMsg = '<span class="text-yellow-600 font-bold">⏳ Sinyal lambat (Mencari...)</span>';

                if(liveGpsText) liveGpsText.innerHTML = errorMsg;
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
        );
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nikInput = document.getElementById('id_karyawan');
        const namaInput = document.getElementById('nama_karyawan');
        const posisiInput = document.getElementById('posisi');
        const btnRefreshGps = document.getElementById('btn-refresh-gps');

        startGPSTracking();

        if(btnRefreshGps) {
            btnRefreshGps.addEventListener('click', function() { startGPSTracking(true); });
        }

        if (nikInput && namaInput && posisiInput) {
            nikInput.addEventListener('input', function() {
                const nik = this.value.trim();
                if (nik.length >= 4) {
                    fetch(`/api/karyawan/${nik}`)
                        .then(response => { if (!response.ok) throw new Error('Not found'); return response.json(); })
                        .then(data => {
                            namaInput.value = data.nama; posisiInput.value = data.posisi;
                            namaInput.classList.remove('bg-slate-50', 'text-slate-900'); namaInput.classList.add('bg-green-50', 'text-green-700', 'border-green-300');
                            posisiInput.classList.remove('bg-slate-50', 'text-slate-900'); posisiInput.classList.add('bg-green-50', 'text-green-700', 'border-green-300');
                        }).catch(error => {
                            namaInput.value = ''; posisiInput.value = '';
                            namaInput.classList.remove('bg-green-50', 'text-green-700', 'border-green-300'); namaInput.classList.add('bg-slate-50', 'text-slate-900');
                            posisiInput.classList.remove('bg-green-50', 'text-green-700', 'border-green-300'); posisiInput.classList.add('bg-slate-50', 'text-slate-900');
                        });
                }
            });
        }

        const video = document.getElementById('camera-stream');
        const canvas = document.getElementById('camera-canvas');
        const fotoInput = document.getElementById('foto_absen');
        const latInput = document.getElementById('latitude');
        const longInput = document.getElementById('longitude');
        const lokasiInput = document.getElementById('lokasi_absen');
        const cameraLoading = document.getElementById('camera-loading');
        const formAbsen = document.getElementById('form-absen-mandiri');
        const btnSubmit = document.getElementById('btn-submit-absen');
        let isSubmitting = false;

        function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                renderCameraError('Browser ini tidak mendukung akses kamera langsung, atau Anda sedang mengakses tanpa HTTPS/localhost.');
                return;
            }

            cameraLoading.classList.remove('hidden');
            cameraLoading.innerHTML = `
                <div class="flex flex-col items-center justify-center p-3 text-center">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2 text-blue-500"></i>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Menghubungkan Kamera...</p>
                </div>
            `;

            const handleSuccess = function(stream) {
                video.srcObject = stream;
                video.onloadedmetadata = function() {
                    video.play().catch(e => console.warn('Play error:', e));
                    video.classList.remove('hidden');
                    cameraLoading.classList.add('hidden');
                };
            };

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(handleSuccess)
                .catch(function(err) {
                    console.warn('facingMode user gagal, mencoba fallback video: true...', err);
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(handleSuccess)
                        .catch(function(err2) {
                            console.error('Semua percobaan akses kamera gagal:', err2);
                            let reason = 'Izin kamera ditolak atau kamera tidak terdeteksi.';
                            if (err2.name === 'NotAllowedError' || err2.name === 'PermissionDeniedError') {
                                reason = 'Akses kamera ditolak. Harap klik izin (Allow) pada browser Anda.';
                            } else if (err2.name === 'NotFoundError' || err2.name === 'DevicesNotFoundError') {
                                reason = 'Tidak ada perangkat webcam / kamera yang terdeteksi.';
                            }
                            renderCameraError(reason);
                        });
                });
        }

        function renderCameraError(reason) {
            video.classList.add('hidden');
            cameraLoading.classList.remove('hidden');
            cameraLoading.innerHTML = `
                <div class="flex flex-col items-center justify-center p-3 text-center max-w-[210px]">
                    <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-2 shadow-sm border border-red-200">
                        <i class="fas fa-video-slash text-base"></i>
                    </div>
                    <p class="text-[11px] font-black uppercase text-red-600 mb-1 leading-tight">KAMERA BELUM AKTIF</p>
                    <p class="text-[9px] text-slate-500 leading-tight mb-2.5">${reason}</p>
                    <button type="button" id="btn-retry-camera" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indofood-blue hover:bg-blue-800 text-white text-[10px] font-bold rounded-lg shadow-sm transition">
                        <i class="fas fa-sync-alt text-[9px]"></i> Coba Lagi
                    </button>
                </div>
            `;
            const btnRetry = document.getElementById('btn-retry-camera');
            if (btnRetry) {
                btnRetry.addEventListener('click', function(e) {
                    e.preventDefault();
                    startCamera();
                });
            }
        }

        startCamera();

        formAbsen.addEventListener('submit', function(e) {
            e.preventDefault();
            if (isSubmitting) return;

            const posisiValue = posisiInput.value.trim();
            const namaValue = namaInput.value.trim();
            const userPosition = posisiValue.toLowerCase();

            if (!namaValue || !userPosition) {
                Swal.fire({
                    title: 'DATA BELUM LENGKAP!',
                    html: `<div class="text-left"><p>Pastikan:</p><ul class="list-disc list-inside mt-2"><li>${namaValue ? '✓' : '✗'} Nama sudah ter-load dari database</li><li>${userPosition ? '✓' : '✗'} Posisi/Jabatan sudah ter-load</li></ul><p class="mt-3 text-sm text-blue-600"><b>Tip:</b> Tunggu field berubah warna hijau setelah input NIK</p></div>`,
                    icon: 'warning', confirmButtonColor: '#004a99'
                });
                return;
            }

            if (!video.classList.contains('hidden') && video.videoWidth > 0) {
                isSubmitting = true;
                btnSubmit.disabled = true;
                btnSubmit.classList.remove('hover:bg-blue-800', 'hover:-translate-y-0.5');
                btnSubmit.classList.add('opacity-70', 'cursor-wait');
                btnSubmit.innerHTML = '<i class="fas fa-satellite-dish fa-spin text-base"></i><span>VALIDASI LOKASI...</span>';

                function processSubmission(userLat, userLng) {
                    const zoneInfo = getClosestZoneWithDetails(userLat, userLng);
                    const closestZone = zoneInfo.zone;
                    const minDistance = zoneInfo.distance;

                    if (!closestZone) {
                        resetSubmitButton();
                        Swal.fire({ title: 'ZONA TIDAK TERDETEKSI', html: `Sistem tidak bisa mendeteksi zona di lokasi Anda.<br>Koordinat: <b>${userLat.toFixed(6)}, ${userLng.toFixed(6)}</b>`, icon: 'error', confirmButtonColor: '#d33' });
                        return;
                    }

                    if (!zoneInfo.inBounds && !zoneInfo.inRadius) {
                        resetSubmitButton();
                        Swal.fire({ title: 'DI LUAR AREA KERJA!', html: `Anda berada di luar area yang diizinkan.<br>Batas toleransi maksimal adalah ${MAX_RADIUS_METERS} Meter.<br><br><small class="text-blue-600 font-bold">Pastikan posisi Anda masuk dalam wilayah kerja yang telah ditentukan!</small>`, icon: 'error', confirmButtonColor: '#d33' });
                        return;
                    }

                    if (closestZone.allowed_positions && closestZone.allowed_positions.length > 0) {
                        const isAllowed = closestZone.allowed_positions.some(role => { return userPosition.includes(role.toLowerCase().trim()); });
                        if (!isAllowed) {
                            resetSubmitButton();
                            Swal.fire({ title: 'AKSES DITOLAK ❌', html: `<b>${closestZone.name}</b> dikhususkan untuk:<br><br><span class="text-blue-600 font-bold">${closestZone.allowed_positions.join(', ').toUpperCase()}</span><br><br>Posisi Anda: <b>${posisiValue}</b>`, icon: 'error', confirmButtonColor: '#d33' });
                            return;
                        }
                    }

                    latInput.value = userLat;
                    longInput.value = userLng;
                    if(lokasiInput) lokasiInput.value = closestZone.name;

                    const context = canvas.getContext('2d');
                    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    fotoInput.value = canvas.toDataURL('image/jpeg', 0.8);
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin text-base"></i><span>MEMPROSES DATA...</span>';

                    formAbsen.submit();
                }

                function resetSubmitButton() {
                    isSubmitting = false; btnSubmit.disabled = false;
                    btnSubmit.classList.remove('opacity-70', 'cursor-wait'); btnSubmit.classList.add('hover:bg-blue-800', 'hover:-translate-y-0.5');
                    btnSubmit.innerHTML = '<i class="fas fa-shield-alt text-base"></i><span>SIMPAN ABSENSI</span>';
                }

                if (lastValidCoordinates.lat !== null && lastValidCoordinates.lng !== null) {
                    setTimeout(() => {
                        processSubmission(lastValidCoordinates.lat, lastValidCoordinates.lng);
                    }, 500);
                } else {
                    resetSubmitButton();
                    Swal.fire({
                        title: 'Satelit Belum Terkunci 📡',
                        html: `Sistem belum mendapatkan titik koordinat Anda.<br><br>Pastikan indikator Radar Live sudah mendeteksi lokasi Anda sebelum menyimpan absen.`,
                        icon: 'warning',
                        confirmButtonColor: '#004a99'
                    });
                }

            } else {
                Swal.fire({
                    title: 'KAMERA BELUM AKTIF!',
                    html: `<div class="text-left text-sm text-slate-600">
                        <p class="mb-2">Absensi membutuhkan foto wajah secara langsung untuk validasi kehadiran.</p>
                        <p class="mb-2 font-bold text-slate-800">Cara mengaktifkan kamera:</p>
                        <ol class="list-decimal list-inside space-y-1 text-xs">
                            <li>Klik ikon <b>Gembok / Setelan Situs</b> di sebelah kiri URL browser.</li>
                            <li>Pastikan izin <b>Kamera</b> disetel ke <b>Izinkan (Allow)</b>.</li>
                            <li>Klik tombol <b>Coba Lagi</b> di kotak pemindaian wajah atau segarkan halaman.</li>
                        </ol>
                    </div>`,
                    icon: 'warning',
                    confirmButtonColor: '#004a99',
                    confirmButtonText: 'Saya Mengerti'
                });
            }
        });
    });
</script>
