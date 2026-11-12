@php
    $allUsers = \App\Models\User::orderBy('name', 'asc')->get();
    $totalUsers = $allUsers->count();
    $trashedUsers = \App\Models\User::onlyTrashed()->get();
    $employeesMaster = \App\Models\Employee::orderBy('nama', 'asc')->get();
@endphp

<div id="tab-kelola-user" class="tab-content hidden w-full max-w-full flex-1 flex flex-col gap-4">

    {{-- ========================================================= --}}
    {{-- TABEL MASTER PENGGUNA (DONEZO / DASHBOARD STYLE CARD)     --}}
    {{-- ========================================================= --}}
    <div class="glass-card rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col flex-1 min-h-0 bg-white w-full max-w-full">
        
        {{-- TIER 1: Header Utama --}}
        <div class="px-5 sm:px-7 py-3.5 sm:py-4 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">Manajemen Pengguna Aplikasi</h3>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span id="userTableTotal">{{ $totalUsers }}</span> User
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola akun kredensial pengguna, pembagian hak akses (role), dan integrasi NIK karyawan</p>
            </div>

            {{-- Tombol Aksi Tambah User Baru (Selalu Utuh & Tidak Terpotong) --}}
            <div class="shrink-0">
                <button type="button" 
                        onclick="openAddUserModal()" 
                        class="px-4 sm:px-5 rounded-xl text-white text-xs font-bold transition-all shadow-sm hover:opacity-95 active:scale-[0.98] flex items-center gap-2 cursor-pointer shrink-0" 
                        style="background-color: #004a99 !important; height: 38px;" 
                        title="Tambah Pengguna Baru">
                    <i class="fas fa-user-plus text-sm"></i>
                    <span>Tambah Pengguna</span>
                </button>
            </div>
        </div>

        {{-- TIER 2: Toolbar Pencarian & Filter Tabel --}}
        <div class="px-5 sm:px-7 py-2.5 bg-slate-50 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
            <div class="flex items-center gap-2.5 flex-wrap flex-1 min-w-0">
                {{-- Search Box Live --}}
                <div class="relative flex items-center min-w-[200px] sm:min-w-[240px] max-w-sm flex-1">
                    <i class="fas fa-search text-slate-400 text-xs absolute left-3.5 pointer-events-none"></i>
                    <input type="text" 
                           id="userSearch" 
                           onkeyup="filterUserTable()" 
                           oninput="filterUserTable()" 
                           placeholder="Cari Nama, Email, atau NIK..." 
                           class="w-full pl-9 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                           style="height: 38px;">
                </div>

                {{-- Filter Role Dropdown --}}
                <div class="relative flex items-center shrink-0">
                    <select id="roleFilter" 
                            onchange="filterUserTable()" 
                            class="text-xs pl-3 pr-8 rounded-xl bg-white border border-slate-300 outline-none shadow-sm cursor-pointer font-semibold text-slate-700 focus:border-blue-500 transition-all appearance-none" 
                            style="height: 38px;">
                        <option value="all">Semua Hak Akses (Role)</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin_absensi">Admin Absensi</option>
                        <option value="admin_stationery">Admin Stationery</option>
                        <option value="admin_penilaian">Admin Penilaian</option>
                        <option value="admin_internal">Admin Internal Indofood</option>
                        <option value="admin_outsourcing">Admin Outsourcing</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[9px] absolute right-3 pointer-events-none"></i>
                </div>

                {{-- Filter NIK Status --}}
                <div class="relative flex items-center shrink-0">
                    <select id="nikFilter" 
                            onchange="filterUserTable()" 
                            class="text-xs pl-3 pr-8 rounded-xl bg-white border border-slate-300 outline-none shadow-sm cursor-pointer font-semibold text-slate-700 focus:border-blue-500 transition-all appearance-none" 
                            style="height: 38px;">
                        <option value="all">Status NIK: Semua</option>
                        <option value="linked">Terikat NIK</option>
                        <option value="unlinked">Belum Ada NIK</option>
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[9px] absolute right-3 pointer-events-none"></i>
                </div>

                {{-- Tombol Reset Filter --}}
                <button type="button" 
                        id="btnResetUserFilter" 
                        onclick="resetUserFilters()" 
                        class="hidden px-3 rounded-xl bg-slate-200/90 hover:bg-red-50 hover:text-red-600 text-slate-600 text-xs font-bold items-center gap-1.5 transition-all cursor-pointer shrink-0" 
                        style="height: 38px;" 
                        title="Reset Filter & Pencarian">
                    <i class="fas fa-rotate-left text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>

            {{-- Right Hint / Indicator --}}
            <div class="text-[11px] text-slate-400 font-medium hidden sm:flex items-center gap-1.5 shrink-0">
                <i class="fas fa-sliders-h text-slate-300 text-xs"></i>
                <span>Menampilkan <strong id="userFilteredCount" class="text-slate-700 font-bold">{{ $totalUsers }}</strong> akun</span>
            </div>
        </div>

        {{-- Area Tabel Pengguna (Horizontal Scroll Aman & Tidak Terpotong) --}}
        <div class="overflow-x-auto flex-1 bg-white relative w-full table-scroll-container">
            <table class="w-full text-left border-collapse table-user-clean min-w-[750px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-4 text-center w-14 shrink-0">NO</th>
                        <th class="py-3.5 px-4 min-w-[200px]">PROFIL PENGGUNA</th>
                        <th class="py-3.5 px-4 min-w-[200px]">EMAIL / USERNAME</th>
                        <th class="py-3.5 px-4 text-center min-w-[170px]">HAK AKSES (ROLE)</th>
                        <th class="py-3.5 px-4 text-center min-w-[150px]">INTEGRASI NIK</th>
                        <th class="py-3.5 px-6 text-center w-32 shrink-0">AKSI</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-slate-100 text-slate-700 bg-white text-xs">
                    @forelse($allUsers as $index => $user)
                    @php
                        $roleClean = strtolower(trim($user->role));
                        $isSuper = ($roleClean === 'superadmin' || strtoupper(trim($user->name)) === 'ADMIN SUPER');
                        $userInitial = strtoupper(substr(trim($user->name), 0, 2));
                    @endphp
                    <tr class="hover:bg-blue-50/40 transition-colors user-row" 
                        data-name="{{ $user->name }}" 
                        data-email="{{ $user->email }}" 
                        data-role="{{ $roleClean }}" 
                        data-nik="{{ $user->nik ?? '' }}">
                        {{-- Kolom No --}}
                        <td class="py-3.5 px-4 text-center text-xs font-semibold text-slate-400 user-row-no shrink-0">
                            {{ $index + 1 }}
                        </td>

                        {{-- Kolom Profil Pengguna (Avatar Inisial + Nama Lengkap + Jabatan) --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 min-w-[36px] min-h-[36px] rounded-full bg-blue-100 text-indofood-blue font-extrabold text-xs flex items-center justify-center uppercase ring-2 ring-white shadow-2xs shrink-0">
                                    {{ $userInitial }}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-800 text-xs sm:text-sm leading-snug">
                                            {{ $user->name }}
                                        </span>
                                        @if($user->id === Auth::id())
                                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold shrink-0">
                                                Anda
                                            </span>
                                        @endif
                                    </div>
                                    @if($user->employee && !empty($user->employee->jabatan))
                                        <span class="text-[11px] font-medium text-slate-400 truncate mt-0.5">
                                            <i class="fas fa-briefcase text-[10px] text-slate-300 mr-1"></i> {{ $user->employee->jabatan }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Email / Username --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-slate-300 text-xs shrink-0"></i>
                                <span class="font-mono text-xs text-slate-600 font-medium select-all">
                                    {{ $user->email }}
                                </span>
                            </div>
                        </td>

                        {{-- Kolom Hak Akses (Role) --}}
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($isSuper)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Super Admin
                                </span>
                            @elseif($roleClean == 'admin_absensi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-800 border border-green-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Admin Absensi
                                </span>
                            @elseif($roleClean == 'admin_stationery')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-600"></span> Admin Stationery
                                </span>
                            @elseif($roleClean == 'admin_penilaian')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-800 border border-purple-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Admin Penilaian
                                </span>
                            @elseif($roleClean == 'admin_internal')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-800 border border-indigo-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span> Admin Internal
                                </span>
                            @elseif($roleClean == 'admin_outsourcing')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Admin Outsourcing
                                </span>
                            @elseif($roleClean == 'karyawan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Karyawan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-bold shadow-2xs">
                                    {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                </span>
                            @endif
                        </td>

                        {{-- Kolom Status NIK Karyawan --}}
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if(!empty($user->nik))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50/90 text-blue-700 border border-blue-100 font-mono font-bold text-xs" title="NIK Terdaftar di Database">
                                    <i class="fas fa-id-badge text-blue-500 text-[11px]"></i>
                                    <span>{{ $user->nik }}</span>
                                </span>
                            @elseif($roleClean === 'karyawan')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[11px] font-bold" title="Karyawan belum terikat NIK">
                                    <i class="fas fa-triangle-exclamation text-red-500 text-[10px]"></i>
                                    <span>Belum Terikat NIK</span>
                                </span>
                            @else
                                <span class="text-slate-300 font-medium text-xs">-</span>
                            @endif
                        </td>

                        {{-- Kolom Tombol Aksi (Padding Nyaman & Tombol Utuh) --}}
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Edit --}}
                                <button type="button" 
                                        onclick="openEditUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->nik ?? '' }}')" 
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer shrink-0" 
                                        title="Edit Profil & Akses Pengguna">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>

                                {{-- Tombol Hapus (Diproteksi agar tidak bisa hapus akun sendiri) --}}
                                @if($user->id !== Auth::id())
                                <form action="{{ url('/users/' . $user->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Pindahkan akun pengguna {{ addslashes($user->name) }} ke tong sampah?');" 
                                      class="inline m-0 shrink-0">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer shrink-0" 
                                            title="Pindahkan ke Tong Sampah">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-16 text-center text-slate-400 font-semibold italic border-none text-xs">
                            <i class="fas fa-users-slash mr-2 text-slate-300 text-base"></i> Belum ada data pengguna yang terdaftar.
                        </td>
                    </tr>
                    @endforelse

                    {{-- Baris Kosong Saat Pencarian Tidak Menemukan Hasil --}}
                    <tr id="noUserMatchRow" class="hidden">
                        <td colspan="6" class="p-14 text-center text-slate-400 font-semibold italic border-none text-xs">
                            <i class="fas fa-search mr-2 text-slate-300 text-sm"></i> Tidak ada pengguna yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Footer Informasi --}}
        <div class="px-5 sm:px-7 py-3.5 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 shrink-0">
            <p class="text-xs text-slate-500 font-medium">
                Pusat data autentikasi dan manajemen otorisasi sistem PT Indofood CBP Sukses Makmur Tbk
            </p>
            <div class="text-xs text-slate-400 font-semibold flex items-center gap-2 shrink-0">
                <i class="fas fa-shield-halved text-blue-500"></i>
                <span>Keamanan Hak Akses Terlindungi</span>
            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- TONG SAMPAH (TRASHED USERS - ACCORDION / COLLAPSIBLE)     --}}
    {{-- ========================================================= --}}
    @if($trashedUsers->count() > 0)
    <div class="glass-card rounded-2xl border border-red-200/80 shadow-sm overflow-hidden flex flex-col bg-white shrink-0 mt-2 w-full max-w-full">
        <div class="px-5 sm:px-7 py-3.5 bg-red-50/60 border-b border-red-100 flex flex-wrap items-center justify-between gap-3 cursor-pointer" onclick="toggleTrashedUsersSection()">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fas fa-trash-can"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs sm:text-sm font-bold text-red-800 leading-tight">Tong Sampah (Pengguna Terhapus)</h4>
                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold border border-red-200">
                            {{ $trashedUsers->count() }} Akun
                        </span>
                    </div>
                    <p class="text-[11px] text-red-500 font-medium mt-0.5">Akun di bawah ini dapat dikembalikan atau dihapus permanen dari basis data</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-red-600 hover:text-red-800 transition-colors shrink-0">
                <span id="trashToggleText">Buka Rincian</span>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="trashChevron"></i>
            </div>
        </div>

        <div id="trashedUsersContent" class="hidden overflow-x-auto p-4 sm:p-6 bg-red-50/20 w-full">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="text-[11px] uppercase font-bold text-red-700/80 border-b border-red-200/60">
                        <th class="py-2.5 px-3 text-center w-12 shrink-0">NO</th>
                        <th class="py-2.5 px-3 min-w-[180px]">NAMA PENGGUNA</th>
                        <th class="py-2.5 px-3 min-w-[180px]">EMAIL / USERNAME</th>
                        <th class="py-2.5 px-3 min-w-[140px]">ROLE LAMA</th>
                        <th class="py-2.5 px-3 text-right min-w-[180px] shrink-0">AKSI PEMULIHAN</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-red-100/60">
                    @foreach($trashedUsers as $trashed)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="py-3 px-3 text-center font-bold text-red-400">{{ $loop->iteration }}</td>
                        <td class="py-3 px-3 font-bold text-slate-800">{{ $trashed->name }}</td>
                        <td class="py-3 px-3 font-mono text-slate-600">{{ $trashed->email }}</td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">
                                {{ $trashed->role }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ url('/users/restore/' . $trashed->id) }}" method="POST" class="m-0 shrink-0">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-white text-xs font-bold hover:opacity-90 transition shadow-2xs flex items-center gap-1.5 cursor-pointer" style="background-color: #16a34a !important;" title="Pulihkan Pengguna">
                                        <i class="fas fa-rotate-left text-[11px]"></i>
                                        <span>Kembalikan</span>
                                    </button>
                                </form>
                                <form action="{{ url('/users/force-delete/' . $trashed->id) }}" method="POST" onsubmit="return confirm('Hapus permanen akun {{ addslashes($trashed->name) }}? Tindakan ini tidak dapat dibatalkan!');" class="m-0 shrink-0">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Hapus Selamanya">
                                        <i class="fas fa-trash text-[11px]"></i>
                                        <span>Hapus Selamanya</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ========================================================= --}}
{{-- MODAL TAMBAH USER BARU (DONEZO STYLE ROOT TELEPORTED)     --}}
{{-- ========================================================= --}}
<div id="addUserModal" style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important; overflow-y: auto !important;" class="hidden items-center justify-center p-3 sm:p-4 md:p-6">
    <div style="background-color: #ffffff !important; opacity: 1 !important; position: relative !important; z-index: 1000000 !important; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important; max-width: 520px; max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem); margin: auto; display: flex; flex-direction: column;" class="w-full rounded-3xl overflow-hidden">
        
        {{-- Modal Header --}}
        <div class="px-6 sm:px-7 py-4.5 flex justify-between items-center text-white font-bold text-sm tracking-wide shrink-0" style="background-color: #004a99 !important; flex-shrink: 0;">
            <span class="flex items-center gap-2.5">
                <i class="fas fa-user-plus text-base"></i>
                <span class="text-base font-bold">Tambah Pengguna Baru</span>
            </span>
            <button type="button" onclick="closeAddUserModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form action="{{ route('users.store') }}" method="POST" class="p-6 sm:p-7 space-y-4.5 overflow-y-auto flex-1 modal-scrollable-body" style="background-color: #ffffff !important; min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf

            {{-- Role --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Hak Akses (Role) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="role" id="add_user_role" required class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-blue-500 shadow-sm transition-all appearance-none" style="height: 42px;" onchange="toggleNikField('add')">
                        <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                        <option value="admin_absensi" {{ old('role') == 'admin_absensi' ? 'selected' : '' }}>Admin Absensi</option>
                        <option value="admin_stationery" {{ old('role') == 'admin_stationery' ? 'selected' : '' }}>Admin Stationery</option>
                        <option value="admin_penilaian" {{ old('role') == 'admin_penilaian' ? 'selected' : '' }}>Admin Penilaian</option>
                        <option value="admin_internal" {{ old('role') == 'admin_internal' ? 'selected' : '' }}>Admin Internal Indofood</option>
                        <option value="admin_outsourcing" {{ old('role') == 'admin_outsourcing' ? 'selected' : '' }}>Admin Outsourcing</option>
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            {{-- Auto-sync dropdown from Master Employee --}}
            <div id="add_nik_container" class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Ambil dari Data Karyawan <span class="text-slate-400 font-normal lowercase">(opsional - otomatis isi)</span>
                </label>
                <div class="relative">
                    <select id="add_user_employee_select" class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:outline-none focus:border-blue-500 shadow-sm transition-all appearance-none" style="height: 42px;" onchange="syncEmployeeToInputs(this, 'add_user_nik', 'add_user_name')">
                        <option value="">-- Tambah Manual (Tanpa Sinkron Master) --</option>
                        @foreach($employeesMaster as $emp)
                            <option value="{{ $emp->nik }}" data-nama="{{ $emp->nama }}" {{ old('nik') == $emp->nik ? 'selected' : '' }}>
                                {{ $emp->nik }} - {{ $emp->nama }} ({{ $emp->jabatan }})
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
                <p class="text-[11px] text-slate-400">Pilih dari database untuk mengisi NIK & Nama secara otomatis.</p>
            </div>

            {{-- Input NIK --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    NIK Karyawan / Admin <span class="text-red-500 font-bold" id="add_nik_req_badge">(Wajib untuk Karyawan)</span>
                </label>
                <input type="text" 
                       name="nik" 
                       id="add_user_nik" 
                       value="{{ old('nik') }}" 
                       placeholder="Contoh: 123456" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Nama --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="name" 
                       id="add_user_name" 
                       value="{{ old('name') }}" 
                       required 
                       placeholder="Contoh: AHMAD FAUZI" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Email --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Alamat Email / Username Login <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="nama@indofood.co.id atau username" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Password --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Password <span class="text-red-500 text-[11px]">(Min. 4 Karakter) *</span></label>
                <div class="relative flex items-center">
                    <input type="password" 
                           name="password" 
                           id="add_user_password" 
                           required 
                           minlength="4" 
                           placeholder="Masukkan kata sandi akun" 
                           class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                           style="height: 42px;">
                    <button type="button" 
                            onclick="togglePasswordVisibility('add_user_password', this)" 
                            class="absolute right-3.5 text-slate-400 hover:text-blue-600 focus:outline-none cursor-pointer">
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex gap-3 pt-3 shrink-0" style="flex-shrink: 0;">
                <button type="button" 
                        onclick="closeAddUserModal()" 
                        class="flex-1 py-2.5 rounded-xl border border-slate-300 text-xs font-bold hover:bg-slate-50 transition text-slate-700 cursor-pointer"
                        style="height: 42px;">
                    BATAL
                </button>
                <button type="submit" 
                        class="flex-1 py-2.5 rounded-xl text-white text-xs font-bold hover:opacity-95 transition shadow-sm cursor-pointer" 
                        style="background-color: #004a99 !important; height: 42px;">
                    SIMPAN PENGGUNA
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL EDIT USER (DONEZO STYLE ROOT TELEPORTED)            --}}
{{-- ========================================================= --}}
<div id="editUserModal" style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important; overflow-y: auto !important;" class="hidden items-center justify-center p-3 sm:p-4 md:p-6">
    <div style="background-color: #ffffff !important; opacity: 1 !important; position: relative !important; z-index: 1000000 !important; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important; max-width: 520px; max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem); margin: auto; display: flex; flex-direction: column;" class="w-full rounded-3xl overflow-hidden">
        
        {{-- Modal Header --}}
        <div class="px-6 sm:px-7 py-4.5 flex justify-between items-center text-white font-bold text-sm tracking-wide shrink-0" style="background-color: #004a99 !important; flex-shrink: 0;">
            <span class="flex items-center gap-2.5">
                <i class="fas fa-user-edit text-base"></i>
                <span class="text-base font-bold">Edit Akun Pengguna</span>
            </span>
            <button type="button" onclick="closeEditUserModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="editUserForm" method="POST" class="p-6 sm:p-7 space-y-4.5 overflow-y-auto flex-1 modal-scrollable-body" style="background-color: #ffffff !important; min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf 
            @method('PUT')

            {{-- Role --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Hak Akses (Role) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="role" id="edit_user_role" required class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-blue-500 shadow-sm transition-all appearance-none" style="height: 42px;" onchange="toggleNikField('edit')">
                        <option value="karyawan">Karyawan</option>
                        <option value="superadmin">Super Admin (Akses Penuh)</option>
                        <option value="admin_absensi">Admin Absensi</option>
                        <option value="admin_stationery">Admin Stationery</option>
                        <option value="admin_penilaian">Admin Penilaian</option>
                        <option value="admin_internal">Admin Internal Indofood</option>
                        <option value="admin_outsourcing">Admin Outsourcing</option>
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            {{-- Auto-sync dropdown from Master Employee --}}
            <div id="edit_nik_container" class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Ambil dari Data Karyawan <span class="text-slate-400 font-normal lowercase">(opsional - sinkron data)</span>
                </label>
                <div class="relative">
                    <select id="edit_user_employee_select" class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:outline-none focus:border-blue-500 shadow-sm transition-all appearance-none" style="height: 42px;" onchange="syncEmployeeToInputs(this, 'edit_user_nik', 'edit_user_name')">
                        <option value="">-- Input Manual / Tanpa Data Karyawan --</option>
                        @foreach($employeesMaster as $emp)
                            <option value="{{ $emp->nik }}" data-nama="{{ $emp->nama }}">
                                {{ $emp->nik }} - {{ $emp->nama }} ({{ $emp->jabatan }})
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
                <p class="text-[11px] text-slate-400">Pilih untuk menyelaraskan NIK & Nama dengan master profil karyawan.</p>
            </div>

            {{-- Input NIK --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    NIK Karyawan / Admin <span class="text-red-500 font-bold" id="edit_nik_req_badge">(Wajib untuk Karyawan)</span>
                </label>
                <input type="text" 
                       name="nik" 
                       id="edit_user_nik" 
                       placeholder="Contoh: 123456" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Nama --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="name" 
                       id="edit_user_name" 
                       required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Email --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Alamat Email / Username Login <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="email" 
                       id="edit_user_email" 
                       required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                       style="height: 42px;">
            </div>

            {{-- Input Password Baru --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Password Baru <span class="text-slate-400 font-normal text-[11px]">(Kosongkan jika tidak diganti)</span></label>
                <div class="relative flex items-center">
                    <input type="password" 
                           name="password" 
                           id="edit_user_password" 
                           minlength="4" 
                           placeholder="Minimal 4 karakter jika ingin diubah" 
                           class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                           style="height: 42px;">
                    <button type="button" 
                            onclick="togglePasswordVisibility('edit_user_password', this)" 
                            class="absolute right-3.5 text-slate-400 hover:text-blue-600 focus:outline-none cursor-pointer">
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex gap-3 pt-3 shrink-0" style="flex-shrink: 0;">
                <button type="button" 
                        onclick="closeEditUserModal()" 
                        class="flex-1 py-2.5 rounded-xl border border-slate-300 text-xs font-bold hover:bg-slate-50 transition text-slate-700 cursor-pointer"
                        style="height: 42px;">
                    BATAL
                </button>
                <button type="submit" 
                        class="flex-1 py-2.5 rounded-xl text-white text-xs font-bold hover:opacity-95 transition shadow-sm cursor-pointer" 
                        style="background-color: #004a99 !important; height: 42px;">
                    PERBARUI PENGGUNA
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .table-user-clean th {
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        color: #475569 !important;
        background-color: #f8fafc !important;
        white-space: nowrap !important;
    }
    .table-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
        -webkit-overflow-scrolling: touch;
    }
    .table-scroll-container::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 9999px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }
</style>

<script>
    function ensureModalsTeleported() {
        ['addUserModal', 'editUserModal'].forEach(function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });
    }

    function openAddUserModal() {
        ensureModalsTeleported();
        const modal = document.getElementById('addUserModal');
        if (modal) {
            const empSelect = document.getElementById('add_user_employee_select');
            if (empSelect) empSelect.value = '';
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }

    function closeAddUserModal() {
        const modal = document.getElementById('addUserModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function openEditUserModal(id, name, email, role, nik = '') {
        ensureModalsTeleported();
        const modal = document.getElementById('editUserModal');
        if (modal) {
            document.getElementById('editUserForm').action = '/users/' + id;
            document.getElementById('edit_user_name').value = name;
            document.getElementById('edit_user_email').value = email;
            document.getElementById('edit_user_role').value = role;
            document.getElementById('edit_user_nik').value = nik || '';
            const empSelect = document.getElementById('edit_user_employee_select');
            if (empSelect) {
                empSelect.value = nik || '';
            }

            const passInput = document.getElementById('edit_user_password');
            if (passInput) {
                passInput.value = '';
                passInput.type = 'password';
                const icon = passInput.nextElementSibling ? passInput.nextElementSibling.querySelector('i') : null;
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }

    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function syncEmployeeToInputs(selectEl, targetNikInputId, targetNameInputId) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const nikInput = document.getElementById(targetNikInputId);
        const nameInput = document.getElementById(targetNameInputId);
        if (selectedOption && selectedOption.value) {
            if (nikInput) nikInput.value = selectedOption.value;
            if (nameInput && selectedOption.dataset.nama) nameInput.value = selectedOption.dataset.nama;
        }
    }

    function toggleNikField(mode) {
        const roleSelect = document.getElementById(mode === 'add' ? 'add_user_role' : 'edit_user_role');
        const badge = document.getElementById(mode === 'add' ? 'add_nik_req_badge' : 'edit_nik_req_badge');
        if (roleSelect && badge) {
            if (roleSelect.value === 'karyawan') {
                badge.textContent = '(Wajib untuk Karyawan)';
                badge.className = 'text-red-500 font-bold';
            } else {
                badge.textContent = '(Opsional)';
                badge.className = 'text-slate-400 font-normal';
            }
        }
    }

    function filterUserTable() {
        const searchVal = (document.getElementById('userSearch')?.value || '').trim().toUpperCase();
        const roleVal = (document.getElementById('roleFilter')?.value || 'all').toLowerCase();
        const nikVal = (document.getElementById('nikFilter')?.value || 'all').toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr.user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = (row.dataset.name || '').toUpperCase();
            const email = (row.dataset.email || '').toUpperCase();
            const nik = (row.dataset.nik || '').toUpperCase();
            const role = (row.dataset.role || '').toLowerCase();
            const hasNik = nik !== '';

            const matchSearch = searchVal === '' || name.includes(searchVal) || email.includes(searchVal) || nik.includes(searchVal);
            const matchRole = roleVal === 'all' || role === roleVal;
            const matchNik = nikVal === 'all' || (nikVal === 'linked' && hasNik) || (nikVal === 'unlinked' && !hasNik);

            if (matchSearch && matchRole && matchNik) {
                row.style.display = '';
                visibleCount++;
                const noCell = row.querySelector('.user-row-no');
                if (noCell) noCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle Empty Row
        const emptyRow = document.getElementById('noUserMatchRow');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        // Update Counter
        const counterEl = document.getElementById('userFilteredCount');
        if (counterEl) counterEl.innerText = visibleCount;

        // Toggle Reset Button
        const btnReset = document.getElementById('btnResetUserFilter');
        if (btnReset) {
            if (searchVal !== '' || roleVal !== 'all' || nikVal !== 'all') {
                btnReset.classList.remove('hidden');
                btnReset.classList.add('inline-flex');
            } else {
                btnReset.classList.add('hidden');
                btnReset.classList.remove('inline-flex');
            }
        }
    }

    function resetUserFilters() {
        const s = document.getElementById('userSearch');
        if (s) s.value = '';
        const r = document.getElementById('roleFilter');
        if (r) r.value = 'all';
        const n = document.getElementById('nikFilter');
        if (n) n.value = 'all';
        filterUserTable();
    }

    function toggleTrashedUsersSection() {
        const content = document.getElementById('trashedUsersContent');
        const text = document.getElementById('trashToggleText');
        const chevron = document.getElementById('trashChevron');
        if (!content) return;

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (text) text.textContent = 'Tutup Rincian';
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            if (text) text.textContent = 'Buka Rincian';
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button ? button.querySelector('i') : null;

        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureModalsTeleported);
    } else {
        ensureModalsTeleported();
    }

    @if($errors->any())
    window.addEventListener('DOMContentLoaded', () => {
        if (typeof switchTab === 'function') {
            switchTab('kelola-user');
        }
        openAddUserModal();
    });
    @endif
</script>
