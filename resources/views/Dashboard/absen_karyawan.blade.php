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
                <div class="mb-6 p-4 sm:p-5 rounded-2xl shadow-sm relative overflow-hidden bg-indofood-blue text-white border border-blue-900/40" style="background-color: #004a99 !important;">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 sm:gap-4">
                            @if(!empty($loggedUser->profile_pic))
                                <img src="{{ asset($loggedUser->profile_pic) }}" alt="Profile" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl object-cover shadow-xs border border-white/20 shrink-0">
                            @else
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center font-black text-lg sm:text-xl shadow-xs shrink-0 bg-white/15 text-white border border-white/20">
                                    <span>{{ strtoupper(substr($boundEmp->nama, 0, 2)) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-base sm:text-lg font-black tracking-wide truncate uppercase text-white m-0">{{ $boundEmp->nama }}</h3>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/15 text-blue-100 border border-white/20">
                                        <i class="fas fa-id-badge text-[10px] text-blue-200"></i> NIK: {{ $loggedUser->nik }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium text-blue-100">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-briefcase text-[11px] text-blue-200"></i>
                                        <span class="font-bold text-white">{{ $boundEmp->jabatan ?? '-' }}</span>
                                    </span>
                                    <span class="hidden sm:inline text-blue-300/50">•</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-[11px] text-blue-200"></i>
                                        <span>Regu <b class="text-white">{{ $boundEmp->regu ?? '-' }}</b></span>
                                    </span>
                                    <span class="hidden sm:inline text-blue-300/50">•</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-building text-[11px] text-blue-200"></i>
                                        <span>Unit <b class="text-white">{{ $boundEmp->org_unit_text ?? '-' }}</b></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center self-start md:self-auto shrink-0 pt-2 md:pt-0 border-t md:border-0 w-full md:w-auto justify-between md:justify-end border-white/10">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 shadow-2xs">
                                <span class="w-2 h-2 rounded-full animate-pulse bg-emerald-400"></span>
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
                <div class="flex flex-col items-center bg-slate-50 p-4 sm:p-5 rounded-3xl border border-slate-100 h-full shadow-inner justify-center">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-indofood-blue flex items-center justify-center text-xs">
                            <i class="fas fa-id-card-alt"></i>
                        </div>
                        <label class="text-[11px] font-black text-slate-700 uppercase tracking-wider text-center leading-tight">
                            Pemindaian Biometrik Wajah
                        </label>
                    </div>

                    {{-- Kotak Kamera dengan Oval & Dynamic Border --}}
                    <div id="camera-box" class="relative bg-slate-900 rounded-3xl border-4 border-slate-300 overflow-hidden shadow-md flex items-center justify-center w-56 h-56 sm:w-60 sm:h-60 mx-auto transition-all duration-300">
                        <div id="camera-loading" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 z-0 bg-slate-900">
                            <i class="fas fa-camera text-3xl mb-2 animate-pulse text-blue-400"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Memuat Kamera...</p>
                        </div>

                        {{-- Stream Video Kamera --}}
                        <video id="camera-stream" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover z-10 hidden transform scale-x-[-1]"></video>

                        {{-- Canvas Overlay untuk Landmark Wajah & Panduan --}}
                        <canvas id="face-overlay-canvas" class="absolute inset-0 w-full h-full object-cover z-20 pointer-events-none transform scale-x-[-1]"></canvas>

                        {{-- Oval Panduan Wajah (Face Guide) --}}
                        <div id="face-guide-oval" class="absolute inset-4 rounded-[45%] border-2 border-dashed border-white/40 pointer-events-none z-30 transition-all duration-300 flex items-center justify-center">
                            {{-- Scanning Beam Line --}}
                            <div id="face-scan-line" class="absolute w-full h-0.5 bg-cyan-400/80 shadow-[0_0_8px_rgba(34,211,238,0.8)] animate-pulse hidden"></div>
                        </div>

                        {{-- Canvas Hidden untuk Capture Final --}}
                        <canvas id="camera-canvas" class="hidden"></canvas>
                    </div>

                    {{-- Status Badge Real-Time --}}
                    <div id="face-status-badge" class="mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold transition-all duration-300 bg-rose-50 text-rose-600 border border-rose-200 shadow-xs">
                        <span id="face-status-dot" class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span id="face-status-text">Menunggu Kamera...</span>
                    </div>

                    <input type="hidden" name="foto_absen" id="foto_absen" required>
                </div>

                @if($isKaryawanRole && !$hasBoundNik)
                    <button type="button" disabled class="w-full flex items-center justify-center gap-2 bg-slate-300 text-slate-500 font-extrabold py-4 rounded-xl uppercase text-sm tracking-widest cursor-not-allowed shrink-0">
                        <i class="fas fa-ban text-lg"></i>
                        <span>AKUN BELUM MEMILIKI NIK</span>
                    </button>
                @else
                    <button type="submit" id="btn-submit-absen" class="w-full flex items-center justify-center gap-2 bg-slate-400 text-white font-extrabold py-4 rounded-xl uppercase text-sm tracking-widest shadow-md transition-all shrink-0 opacity-60 cursor-not-allowed">
                        <i class="fas fa-lock text-lg" id="btn-submit-icon"></i>
                        <span id="btn-submit-text">KUNCI WAJAH UNTUK ABSEN</span>
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

{{-- AI Face Landmark Detection (Google MediaPipe) --}}
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js" crossorigin="anonymous"></script>

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

        // ==============================================================
        // AI BIOMETRIC FACE DETECTION (Google MediaPipe)
        // Mematenkan deteksi wajah utuh: MATA, HIDUNG, dan MULUT
        // ==============================================================
        let faceDetector = null;
        let isFaceLocked = false;
        let isDetecting = false;
        let detectionInterval = null;

        function initFaceDetector() {
            if (typeof FaceDetection === 'undefined') {
                console.warn('Menunggu library MediaPipe FaceDetection termuat...');
                setTimeout(initFaceDetector, 400);
                return;
            }

            try {
                faceDetector = new FaceDetection({
                    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/${file}`
                });

                faceDetector.setOptions({
                    model: 'short',
                    minDetectionConfidence: 0.60
                });

                faceDetector.onResults(onFaceDetectionResults);
                console.log('AI Biometric Face Detector siap digunakan.');
            } catch (err) {
                console.error('Inisialisasi FaceDetector gagal:', err);
            }
        }

        async function processDetectionFrame() {
            if (isDetecting || !faceDetector || !video || video.paused || video.ended || video.videoWidth === 0) return;
            isDetecting = true;
            try {
                await faceDetector.send({ image: video });
            } catch (err) {
                // frame dropped
            } finally {
                isDetecting = false;
            }
        }

        function onFaceDetectionResults(results) {
            const overlayCanvas = document.getElementById('face-overlay-canvas');
            if (!overlayCanvas) return;
            const ctx = overlayCanvas.getContext('2d');
            overlayCanvas.width = video.videoWidth || 320;
            overlayCanvas.height = video.videoHeight || 320;
            ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

            const detections = results.detections || [];

            let mataValid = false;
            let hidungValid = false;
            let mulutValid = false;
            let overallValid = false;
            let statusTitle = 'Mencari Wajah...';
            let statusDesc = 'Posisikan wajah Anda tepat di depan kamera.';
            let statusState = 'none'; // 'none', 'partial', 'valid'

            if (detections.length === 0) {
                statusTitle = 'Wajah Tidak Terlihat';
                statusDesc = 'Bukan wajah manusia atau kamera tertutup.';
                statusState = 'none';
            } else if (detections.length > 1) {
                statusTitle = 'Terdeteksi >1 Orang';
                statusDesc = 'Harap lakukan absensi sendiri tanpa orang lain di kamera.';
                statusState = 'partial';
            } else {
                const det = detections[0];
                const landmarks = det.landmarks || [];
                const box = det.boundingBox;

                if (landmarks.length >= 4) {
                    const rightEye = landmarks[0];
                    const leftEye = landmarks[1];
                    const noseTip = landmarks[2];
                    const mouthCenter = landmarks[3];

                    // 1. Validasi Mata: Kedua mata harus berada di atas hidung & terpisah wajar
                    const eyeDist = Math.hypot(leftEye.x - rightEye.x, leftEye.y - rightEye.y);
                    const eyeLevel = Math.abs(leftEye.y - rightEye.y);
                    if (rightEye.y < noseTip.y && leftEye.y < noseTip.y && eyeDist >= 0.06 && (eyeLevel / (eyeDist || 1)) < 0.45) {
                        mataValid = true;
                    }

                    // 2. Validasi Hidung: Hidung di bawah mata, di atas mulut, dan di posisi tengah
                    const minEyeX = Math.min(rightEye.x, leftEye.x);
                    const maxEyeX = Math.max(rightEye.x, leftEye.x);
                    if (noseTip.y > Math.max(rightEye.y, leftEye.y) && 
                        noseTip.y < mouthCenter.y &&
                        noseTip.x >= minEyeX - 0.08 && noseTip.x <= maxEyeX + 0.08) {
                        hidungValid = true;
                    }

                    // 3. Validasi Mulut: Mulut harus di bawah hidung dengan jarak yang jelas
                    if (mouthCenter.y > noseTip.y && (mouthCenter.y - noseTip.y) >= 0.02) {
                        mulutValid = true;
                    }

                    // 4. Validasi Ukuran Wajah: Wajah harus memenuhi porsi frame (tidak terlalu jauh)
                    const isSizeOk = box && box.width >= 0.20 && box.height >= 0.20;

                    if (mataValid && hidungValid && mulutValid && isSizeOk) {
                        overallValid = true;
                        statusTitle = 'Wajah Terverifikasi ✓';
                        statusDesc = 'Wajah terverifikasi! Tombol absen aktif.';
                        statusState = 'valid';
                    } else if (!isSizeOk) {
                        statusTitle = 'Dekatkan Wajah ke Kamera';
                        statusDesc = 'Posisikan wajah Anda lebih dekat agar memenuhi area oval.';
                        statusState = 'partial';
                    } else {
                        statusTitle = 'Posisikan Wajah ke Kamera';
                        statusDesc = 'Pastikan seluruh wajah terlihat jelas dan tidak tertutup.';
                        statusState = 'partial';
                    }

                    // Gambarkan titik landmark halus pada canvas overlay
                    drawLandmarks(ctx, overlayCanvas.width, overlayCanvas.height, landmarks, overallValid);
                }
            }

            updateFaceUI(statusState, statusTitle, statusDesc, mataValid, hidungValid, mulutValid, overallValid);
        }

        function drawLandmarks(ctx, w, h, landmarks, isValid) {
            const dotColor = isValid ? '#10b981' : '#f59e0b';
            const glowColor = isValid ? 'rgba(16, 185, 129, 0.45)' : 'rgba(245, 158, 11, 0.45)';

            // Titik 0: Mata Kanan, 1: Mata Kiri, 2: Hidung, 3: Mulut
            const points = [landmarks[0], landmarks[1], landmarks[2], landmarks[3]];

            points.forEach((pt) => {
                if (!pt) return;
                const x = pt.x * w;
                const y = pt.y * h;

                ctx.beginPath();
                ctx.arc(x, y, 7, 0, 2 * Math.PI);
                ctx.fillStyle = glowColor;
                ctx.fill();

                ctx.beginPath();
                ctx.arc(x, y, 3.5, 0, 2 * Math.PI);
                ctx.fillStyle = dotColor;
                ctx.fill();
            });
        }

        function updateFaceUI(state, title, desc, mata, hidung, mulut, isValid) {
            const cameraBox = document.getElementById('camera-box');
            const guideOval = document.getElementById('face-guide-oval');
            const scanLine = document.getElementById('face-scan-line');
            const statusBadge = document.getElementById('face-status-badge');
            const statusDot = document.getElementById('face-status-dot');
            const statusText = document.getElementById('face-status-text');

            const btnIcon = document.getElementById('btn-submit-icon');
            const btnText = document.getElementById('btn-submit-text');

            isFaceLocked = isValid;

            if (statusText) statusText.textContent = title;

            if (state === 'valid') {
                if (cameraBox) cameraBox.className = 'relative bg-slate-900 rounded-3xl border-4 border-emerald-500 shadow-xl shadow-emerald-500/20 overflow-hidden flex items-center justify-center w-56 h-56 sm:w-60 sm:h-60 mx-auto transition-all duration-300';
                if (guideOval) guideOval.className = 'absolute inset-4 rounded-[45%] border-2 border-emerald-400 pointer-events-none z-30 transition-all duration-300 flex items-center justify-center';
                if (scanLine) scanLine.classList.add('hidden');

                if (statusBadge) statusBadge.className = 'mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold transition-all duration-300 bg-emerald-50 text-emerald-700 border border-emerald-300 shadow-xs';
                if (statusDot) statusDot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-ping';

                // Buka Kunci Tombol Absen
                if (btnSubmit && !isSubmitting) {
                    btnSubmit.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-slate-400');
                    btnSubmit.classList.add('bg-indofood-blue', 'hover:bg-blue-800', 'hover:-translate-y-0.5', 'cursor-pointer', 'shadow-lg');
                    if (btnIcon) btnIcon.className = 'fas fa-shield-alt text-lg';
                    if (btnText) btnText.textContent = 'SIMPAN ABSENSI';
                }
            } else if (state === 'partial') {
                if (cameraBox) cameraBox.className = 'relative bg-slate-900 rounded-3xl border-4 border-amber-400 shadow-lg shadow-amber-500/20 overflow-hidden flex items-center justify-center w-56 h-56 sm:w-60 sm:h-60 mx-auto transition-all duration-300';
                if (guideOval) guideOval.className = 'absolute inset-4 rounded-[45%] border-2 border-dashed border-amber-400/90 pointer-events-none z-30 transition-all duration-300 flex items-center justify-center';
                if (scanLine) scanLine.classList.remove('hidden');

                if (statusBadge) statusBadge.className = 'mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold transition-all duration-300 bg-amber-50 text-amber-700 border border-amber-300 shadow-xs';
                if (statusDot) statusDot.className = 'w-2 h-2 rounded-full bg-amber-500 animate-pulse';

                // Kunci Tombol Absen secara visual (tetap bisa diklik agar memicu notifikasi peringatan)
                if (btnSubmit && !isSubmitting) {
                    btnSubmit.classList.add('opacity-60', 'cursor-not-allowed', 'bg-slate-400');
                    btnSubmit.classList.remove('bg-indofood-blue', 'hover:bg-blue-800', 'hover:-translate-y-0.5', 'cursor-pointer', 'shadow-lg');
                    if (btnIcon) btnIcon.className = 'fas fa-lock text-lg';
                    if (btnText) btnText.textContent = 'KUNCI WAJAH UNTUK ABSEN';
                }
            } else {
                if (cameraBox) cameraBox.className = 'relative bg-slate-900 rounded-3xl border-4 border-rose-500 shadow-lg shadow-rose-500/20 overflow-hidden flex items-center justify-center w-56 h-56 sm:w-60 sm:h-60 mx-auto transition-all duration-300';
                if (guideOval) guideOval.className = 'absolute inset-4 rounded-[45%] border-2 border-dashed border-rose-400/80 pointer-events-none z-30 transition-all duration-300 flex items-center justify-center';
                if (scanLine) scanLine.classList.remove('hidden');

                if (statusBadge) statusBadge.className = 'mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold transition-all duration-300 bg-rose-50 text-rose-600 border border-rose-200 shadow-xs';
                if (statusDot) statusDot.className = 'w-2 h-2 rounded-full bg-rose-500 animate-pulse';

                // Kunci Tombol Absen secara visual (tetap bisa diklik agar memicu notifikasi peringatan)
                if (btnSubmit && !isSubmitting) {
                    btnSubmit.classList.add('opacity-60', 'cursor-not-allowed', 'bg-slate-400');
                    btnSubmit.classList.remove('bg-indofood-blue', 'hover:bg-blue-800', 'hover:-translate-y-0.5', 'cursor-pointer', 'shadow-lg');
                    if (btnIcon) btnIcon.className = 'fas fa-lock text-lg';
                    if (btnText) btnText.textContent = 'KUNCI WAJAH UNTUK ABSEN';
                }
            }
        }

        function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                renderCameraError('Browser ini tidak mendukung akses kamera langsung, atau Anda sedang mengakses tanpa HTTPS/localhost.');
                return;
            }

            cameraLoading.classList.remove('hidden');
            cameraLoading.innerHTML = `
                <div class="flex flex-col items-center justify-center p-3 text-center">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2 text-blue-400"></i>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Menghubungkan Kamera & AI Biometrik...</p>
                </div>
            `;

            const handleSuccess = function(stream) {
                video.srcObject = stream;
                video.onloadedmetadata = function() {
                    video.play().catch(e => console.warn('Play error:', e));
                    video.classList.remove('hidden');
                    cameraLoading.classList.add('hidden');

                    initFaceDetector();

                    if (detectionInterval) clearInterval(detectionInterval);
                    detectionInterval = setInterval(processDetectionFrame, 120);
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
                    <p class="text-[9px] text-slate-400 leading-tight mb-2.5">${reason}</p>
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

            // ==============================================================
            // PROTEKSI PATEN: HANYA BOLEH WAJAH (MATA, HIDUNG, MULUT VALID)
            // ==============================================================
            if (!isFaceLocked) {
                Swal.fire({
                    title: 'WAJAH TIDAK TERLIHAT! ❌',
                    html: `<div class="text-left text-sm text-slate-700 space-y-2">
                        <p>Sistem mendeteksi bahwa wajah Anda belum terverifikasi atau tidak terlihat di kamera.</p>
                        <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-xs text-rose-900 mt-2">
                            <p class="font-black uppercase tracking-wider mb-1.5 text-rose-800">Petunjuk Absensi Wajah:</p>
                            <ul class="space-y-1">
                                <li class="flex items-center gap-1.5"><i class="fas fa-check-circle text-rose-600"></i> Posisikan wajah Anda tepat di dalam area panduan oval</li>
                                <li class="flex items-center gap-1.5"><i class="fas fa-check-circle text-rose-600"></i> Pastikan wajah terlihat jelas tanpa tertutup masker atau tangan</li>
                                <li class="flex items-center gap-1.5"><i class="fas fa-check-circle text-rose-600"></i> Pastikan pencahayaan ruangan cukup terang</li>
                            </ul>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 italic">Arahkan kamera ke wajah Anda hingga status berubah hijau "Wajah Terverifikasi".</p>
                    </div>`,
                    icon: 'warning',
                    confirmButtonColor: '#004a99',
                    confirmButtonText: 'Saya Mengerti'
                });
                return;
            }

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
                    isSubmitting = false;
                    if (isFaceLocked) {
                        btnSubmit.disabled = false;
                        btnSubmit.classList.remove('opacity-70', 'cursor-wait');
                        btnSubmit.classList.add('hover:bg-blue-800', 'hover:-translate-y-0.5');
                        btnSubmit.innerHTML = '<i class="fas fa-shield-alt text-base"></i><span>SIMPAN ABSENSI</span>';
                    } else {
                        btnSubmit.disabled = true;
                        btnSubmit.classList.add('opacity-60', 'cursor-not-allowed', 'bg-slate-400');
                        btnSubmit.classList.remove('opacity-70', 'cursor-wait', 'hover:bg-blue-800', 'hover:-translate-y-0.5');
                        btnSubmit.innerHTML = '<i class="fas fa-lock text-base"></i><span>KUNCI WAJAH UNTUK ABSEN</span>';
                    }
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
