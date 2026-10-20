<div class="w-full">
    <div class="space-y-6">

        {{-- 0. Banner Selamat Datang (BARU DITAMBAHKAN) --}}
        <section class="bg-gradient-to-r from-blue-700 to-blue-600 rounded-2xl p-6 shadow-md text-white relative overflow-hidden">
            {{-- Elemen Dekorasi --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-y-20 translate-x-20 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-hand-sparkles text-xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold mb-1.5 tracking-wide">Selamat Datang di Dashboard Karyawan! 👋</h2>
                    <p class="text-sm text-blue-100 leading-relaxed font-medium">
                        Ini adalah portal personal untuk memantau aktivitas kehadiran Anda. Cek statistik kehadiran bulanan, pantau riwayat jam masuk dan pulang, serta pastikan Anda selalu disiplin melakukan absensi. Semangat bekerja dan utamakan keselamatan kerja (K3)!
                    </p>
                </div>
            </div>
        </section>

        {{-- 1. Kartu Profil --}}
        <section class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-50 rounded-full -z-0 opacity-50"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center text-2xl shadow-inner">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Profil Anda,</p>

                        {{-- LOGIKA NAMA: Kosong jika belum absen ATAU jika datanya berupa email --}}
                        @php
                            $displayName = '';
                            $rawName = Auth::user()->name ?? '';

                            if (!str_contains($rawName, '@') && ($totalHadir ?? 0) > 0) {
                                $displayName = $rawName;
                            }
                        @endphp

                        <h2 class="text-xl font-bold text-slate-800 leading-tight min-h-[28px]">
                            {{ $displayName }}
                        </h2>

                        <span class="inline-flex items-center mt-1 px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold rounded-md uppercase tracking-wider">
                            <i class="fas fa-id-badge mr-1.5 opacity-70"></i> NIK: {{ Auth::user()->nik ?? (Auth::user()->id ?? '') }}
                        </span>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-slate-500">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                    <p class="text-2xl font-black text-blue-700 tracking-tight">
                        <span id="realtime-clock">--:--</span> <span class="text-sm font-medium text-slate-500">WITA</span>
                    </p>
                </div>
            </div>
        </section>

        {{-- 2. Kartu Statistik --}}
        <section class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-default">
                <div class="w-8 h-8 rounded-full bg-green-50 text-green-500 flex items-center justify-center mb-2">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 leading-none mb-1">{{ $totalHadir ?? 0 }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hadir</p>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-default">
                <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 leading-none mb-1">{{ $totalTelat ?? 0 }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Telat</p>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-default">
                <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-2">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 leading-none mb-1">{{ $totalIzin ?? 0 }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Izin/Sakit</p>
            </div>
        </section>

        {{-- 3. Riwayat Terakhir --}}
        <section class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-700"></i> Aktivitas Terakhir
                </h3>
                <a href="#" class="text-xs font-semibold text-blue-700 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @forelse($riwayatAbsensi ?? [] as $riwayat)
                    <div class="flex justify-between items-center bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-100 p-3 rounded-xl">
                        <div class="flex items-center gap-3">
                            {{-- Cek tipe absen masuk (Hadir) atau keluar (Pulang) berdasarkan database Anda --}}
                            @if(strtolower($riwayat->status) == 'hadir')
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-sign-in-alt text-sm"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-sign-out-alt text-sm"></i>
                                </div>
                            @endif

                            <div>
                                <p class="text-sm font-bold text-slate-700">Absen {{ ucfirst($riwayat->status) }}</p>
                                <p class="text-[10px] text-slate-500 font-medium">{{ $riwayat->lokasi ?? '' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($riwayat->created_at)->format('H:i') }}</span>
                            <span class="block text-[10px] text-slate-400 font-semibold uppercase mt-0.5">
                                {{ \Carbon\Carbon::parse($riwayat->created_at)->isToday() ? 'Hari Ini' : \Carbon\Carbon::parse($riwayat->created_at)->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 flex flex-col items-center justify-center opacity-70">
                        <i class="fas fa-inbox text-3xl text-slate-300 mb-2"></i>
                        <p class="text-sm font-medium text-slate-500">Belum ada riwayat absensi.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const clockElement = document.getElementById('realtime-clock');
        if(clockElement) clockElement.textContent = hours + ':' + minutes;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
