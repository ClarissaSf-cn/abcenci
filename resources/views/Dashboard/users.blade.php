<div id="tab-kelola-user" class="tab-content hidden flex-1 flex flex-col gap-6">
    <div class="flex flex-wrap justify-between items-center bg-white p-5 rounded-xl border border-gray-100 shadow-sm gap-4 shrink-0">
        <div>
            <h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Manajemen Pengguna Aplikasi</h3>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Atur hak akses dan akun sistem</p>
        </div>
        <button type="button" onclick="openAddUserModal()" class="bg-indofood-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-blue-800 transition shadow-md cursor-pointer">
            <i class="fas fa-plus mr-2"></i> Tambah User Baru
        </button>
    </div>

    <div class="glass-card flex-1 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-users text-green-500 mr-2"></i>Daftar Pengguna Aktif</p>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Filter Role</label>
                <select id="roleFilter" onchange="filterUserTable()" class="form-input-modern max-w-xs">
                    <option value="all">Semua Role</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="admin_absensi">Admin Absensi</option>
                    <option value="admin_stationery">Admin Stationery</option>
                    <option value="admin_penilaian">Admin Penilaian</option>
                    <option value="admin_internal">Admin Internal Indofood</option>
                    <option value="admin_outsourcing">Admin Outsourcing</option>
                    <option value="karyawan">Karyawan</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto flex-1 p-6">
            <table class="w-full text-left border-collapse table-bordered">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 text-[10px] uppercase tracking-wider">
                        <th class="p-3 font-black text-center w-12">No</th>
                        <th class="p-3 font-black">Nama Pengguna</th>
                        <th class="p-3 font-black">Email / Username</th>
                        <th class="p-3 font-black text-center">Hak Akses (Role)</th>
                        <th class="p-3 font-black text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @php $employeesMaster = \App\Models\Employee::orderBy('nama')->get(); @endphp
                    @foreach(\App\Models\User::all() as $index => $user)
                    <tr data-role="{{ $user->role }}" class="text-xs font-bold text-slate-700 hover:bg-slate-50 transition border-b border-slate-100">
                        <td class="p-3 text-center">{{ $index + 1 }}</td>
                        <td class="p-3">
                            <span class="font-extrabold text-slate-800">{{ $user->name }}</span>
                            @if($user->nik)
                                <div class="text-[10px] font-bold text-blue-600 flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-id-badge text-blue-500"></i> NIK: {{ $user->nik }}
                                    @if($user->employee)
                                        <span class="text-slate-400">({{ $user->employee->jabatan }})</span>
                                    @endif
                                </div>
                            @elseif($user->role === 'karyawan')
                                <div class="text-[10px] font-bold text-amber-500 flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-exclamation-circle text-amber-400"></i> Belum terikat NIK
                                </div>
                            @endif
                        </td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3 text-center">
                            @if(strtolower($user->role) == 'superadmin' || strtoupper($user->name) == 'ADMIN SUPER')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Super Admin</span>
                            @elseif($user->role == 'admin_absensi')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin Absensi</span>
                            @elseif($user->role == 'admin_stationery')
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin Stationery</span>
                            @elseif($user->role == 'admin_penilaian')
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin Penilaian</span>
                            @elseif($user->role == 'admin_internal')
                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin Internal</span>
                            @elseif($user->role == 'admin_outsourcing')
                                <span class="bg-teal-100 text-teal-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin Outsourcing</span>
                            @elseif($user->role == 'karyawan')
                                <span class="bg-sky-100 text-sky-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Karyawan</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Admin / HRD</span>
                            @endif
                        </td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <button type="button" onclick="openEditUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->nik ?? '' }}')" class="text-blue-400 hover:text-blue-600 transition px-2 cursor-pointer" title="Edit Pengguna">
                                <i class="fas fa-edit"></i>
                            </button>

                            @if($user->id !== Auth::id())
                            <form action="{{ url('/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Pindahkan ke Tong Sampah?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition px-2 cursor-pointer" title="Hapus Pengguna">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @php $trashedUsers = \App\Models\User::onlyTrashed()->get(); @endphp
    @if($trashedUsers->count() > 0)
    <div class="glass-card flex flex-col border-t-4 border-red-400 mt-4">
        <div class="px-6 py-4 border-b border-slate-100 bg-red-50/50">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest"><i class="fas fa-recycle mr-2"></i>Tong Sampah (Terhapus)</p>
        </div>
        <div class="p-6">
            <table class="w-full text-left border-collapse">
                <tbody class="text-xs font-bold text-slate-500">
                    @foreach($trashedUsers as $trashed)
                    <tr class="border-b border-slate-100 italic bg-slate-50/30">
                        <td class="p-3 w-12 text-center text-slate-300">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $trashed->name }}</td>
                        <td class="p-3">{{ $trashed->email }}</td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-3">
                                <form action="{{ url('/users/restore/' . $trashed->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-[9px] font-black uppercase tracking-widest">Kembalikan</button>
                                </form>
                                <form action="{{ url('/users/force-delete/' . $trashed->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 text-[9px] font-black uppercase">Hapus Selamanya</button>
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

{{-- MODAL TAMBAH USER BARU (ROOT-LEVEL STACKING ANTI-BLUR) --}}
<div id="addUserModal" style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important;" class="hidden items-center justify-center p-4">
    <div style="background-color: #ffffff !important; opacity: 1 !important; position: relative !important; z-index: 1000000 !important; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important;" class="w-full max-w-md rounded-2xl overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest" style="background-color: #004a99 !important;">
            <span><i class="fas fa-user-plus mr-2"></i> Tambah Pengguna Baru</span>
            <button type="button" onclick="closeAddUserModal()" class="text-white hover:text-red-200 transition cursor-pointer"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4" style="background-color: #ffffff !important;">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Hak Akses (Role)</label>
                <select name="role" id="add_user_role" required class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;" onchange="toggleNikField('add')">
                    <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                    <option value="admin_absensi" {{ old('role') == 'admin_absensi' ? 'selected' : '' }}>Admin Absensi</option>
                    <option value="admin_stationery" {{ old('role') == 'admin_stationery' ? 'selected' : '' }}>Admin Stationery</option>
                    <option value="admin_penilaian" {{ old('role') == 'admin_penilaian' ? 'selected' : '' }}>Admin Penilaian</option>
                    <option value="admin_internal" {{ old('role') == 'admin_internal' ? 'selected' : '' }}>Admin Internal Indofood</option>
                    <option value="admin_outsourcing" {{ old('role') == 'admin_outsourcing' ? 'selected' : '' }}>Admin Outsourcing</option>
                </select>
            </div>
            <div id="add_nik_container">
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">
                    Ambil dari Data Karyawan <span class="text-slate-400 font-normal lowercase">(opsional - otomatis isi data)</span>
                </label>
                <select id="add_user_employee_select" class="form-input-modern w-full" onchange="syncEmployeeToInputs(this, 'add_user_nik', 'add_user_name')">
                    <option value="">-- Tambah Manual (Tanpa Data Karyawan) --</option>
                    @foreach($employeesMaster as $emp)
                        <option value="{{ $emp->nik }}" data-nama="{{ $emp->nama }}" {{ old('nik') == $emp->nik ? 'selected' : '' }}>
                            {{ $emp->nik }} - {{ $emp->nama }} ({{ $emp->jabatan }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[9px] text-slate-400 mt-1">Bisa pilih untuk isi otomatis, atau ketik manual NIK & nama di bawah ini.</p>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">
                    NIK Karyawan / Admin <span class="text-slate-400 font-normal lowercase">(bisa ketik manual / opsional)</span>
                </label>
                <input type="text" name="nik" id="add_user_nik" value="{{ old('nik') }}" placeholder="Contoh: 123456 (Bisa diketik manual)" class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="add_user_name" value="{{ old('name') }}" required placeholder="Contoh: AHMAD FAUZI" class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Alamat Email / Username</label>
                <input type="text" name="email" value="{{ old('email') }}" required placeholder="nama@indofood.co.id atau username" class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Password <span class="text-rose-500 font-bold text-[9px]">(Min. 4 Karakter)</span></label>
                <div class="relative">
                    <input type="password" name="password" id="add_user_password" required minlength="4" placeholder="Minimal 4 karakter" class="form-input-modern pr-10 w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
                    <button type="button" onclick="togglePasswordVisibility('add_user_password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none cursor-pointer">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAddUserModal()" class="flex-1 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold hover:bg-slate-50 transition text-slate-700 cursor-pointer">BATAL</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white text-xs font-bold hover:opacity-90 transition shadow-md cursor-pointer" style="background-color: #004a99 !important;">SIMPAN</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT USER (ROOT-LEVEL STACKING ANTI-BLUR) --}}
<div id="editUserModal" style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important;" class="hidden items-center justify-center p-4">
    <div style="background-color: #ffffff !important; opacity: 1 !important; position: relative !important; z-index: 1000000 !important; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important;" class="w-full max-w-md rounded-2xl overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest" style="background-color: #2563eb !important;">
            <span><i class="fas fa-user-edit mr-2"></i> Edit Pengguna</span>
            <button type="button" onclick="closeEditUserModal()" class="text-white hover:text-red-200 transition cursor-pointer"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="editUserForm" method="POST" class="p-6 space-y-4" style="background-color: #ffffff !important;">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Hak Akses (Role)</label>
                <select name="role" id="edit_user_role" required class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;" onchange="toggleNikField('edit')">
                    <option value="karyawan">Karyawan</option>
                    <option value="superadmin">Super Admin (Akses Penuh)</option>
                    <option value="admin_absensi">Admin Absensi</option>
                    <option value="admin_stationery">Admin Stationery</option>
                    <option value="admin_penilaian">Admin Penilaian</option>
                    <option value="admin_internal">Admin Internal Indofood</option>
                    <option value="admin_outsourcing">Admin Outsourcing</option>
                </select>
            </div>
            <div id="edit_nik_container">
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">
                    Ambil dari Data Karyawan <span class="text-slate-400 font-normal lowercase">(opsional - otomatis isi data)</span>
                </label>
                <select id="edit_user_employee_select" class="form-input-modern w-full" onchange="syncEmployeeToInputs(this, 'edit_user_nik', 'edit_user_name')">
                    <option value="">-- Input Manual / Tanpa Data Karyawan --</option>
                    @foreach($employeesMaster as $emp)
                        <option value="{{ $emp->nik }}" data-nama="{{ $emp->nama }}">
                            {{ $emp->nik }} - {{ $emp->nama }} ({{ $emp->jabatan }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[9px] text-slate-400 mt-1">Bisa pilih untuk ganti otomatis, atau ubah NIK & nama secara manual.</p>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">
                    NIK Karyawan / Admin <span class="text-slate-400 font-normal lowercase">(bisa diedit manual / opsional)</span>
                </label>
                <input type="text" name="nik" id="edit_user_nik" placeholder="Contoh: 123456 (Bisa diedit manual)" class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="edit_user_name" required class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Alamat Email / Username</label>
                <input type="text" name="email" id="edit_user_email" required class="form-input-modern w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Password Baru <span class="text-slate-400 font-normal">(Kosongkan jika tidak diganti)</span></label>
                <div class="relative">
                    <input type="password" name="password" id="edit_user_password" minlength="4" placeholder="Minimal 4 karakter jika diubah" class="form-input-modern pr-10 w-full" style="background-color: #f8fafc !important; color: #1e293b !important; opacity: 1 !important;">
                    <button type="button" onclick="togglePasswordVisibility('edit_user_password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none cursor-pointer">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditUserModal()" class="flex-1 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold hover:bg-slate-50 transition text-slate-700 cursor-pointer">BATAL</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white text-xs font-bold hover:opacity-90 transition shadow-md cursor-pointer" style="background-color: #2563eb !important;">UPDATE</button>
            </div>
        </form>
    </div>
</div>

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

    function filterUserTable() {
        const selectedRole = document.getElementById('roleFilter').value;
        const rows = document.querySelectorAll('#userTableBody tr');

        rows.forEach(row => {
            const rowRole = row.dataset.role || '';
            row.style.display = (selectedRole === 'all' || rowRole === selectedRole) ? '' : 'none';
        });
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
