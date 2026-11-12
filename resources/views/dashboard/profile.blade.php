<div id="editProfileModal" class="fixed inset-0 flex items-center justify-center p-3 sm:p-5 opacity-0 pointer-events-none transition-all duration-300" style="z-index: 99999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); overflow-y: auto;">
    <div class="bg-white rounded-3xl shadow-2xl w-full transform scale-95 transition-all duration-300 border border-slate-100 flex flex-col my-auto" style="max-width: 520px; max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem); overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35);">

        {{-- Header Modal (Always Pinned at Top) --}}
        <div class="px-5 sm:px-7 py-4 sm:py-4.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0" style="flex-shrink: 0;">
            <div class="flex items-center gap-3 sm:gap-3.5">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center text-sm sm:text-base shadow-md shadow-blue-500/20 shrink-0">
                    <i class="fas fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800 leading-tight">Edit Profil Saya</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">Perbarui informasi akun dan keamanan Anda</p>
                </div>
            </div>
            <button type="button" onclick="closeEditProfileModal()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all cursor-pointer shrink-0" title="Tutup">
                <i class="fas fa-times text-xs sm:text-sm"></i>
            </button>
        </div>

        {{-- Form Content --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 m-0" style="flex: 1 1 auto; min-height: 0; overflow: hidden; display: flex; flex-direction: column;">
            @csrf
            @method('PATCH')

            {{-- Scrollable Form Body --}}
            <div class="p-5 sm:p-7 space-y-5 sm:space-y-6 flex-1 modal-scrollable-body" style="flex: 1 1 auto; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">

                {{-- Interactive Avatar Upload Zone --}}
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-50 via-blue-50/30 to-slate-50 border border-slate-200/80">
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="relative group cursor-pointer shrink-0" onclick="document.getElementById('profile_pic_input').click()" title="Klik untuk ganti foto">
                            <div id="profile_avatar_wrapper" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden ring-4 ring-white shadow-sm flex items-center justify-center bg-gradient-to-tr from-blue-700 to-indigo-600">
                                @if(Auth::user()->profile_pic)
                                    <img id="profile_avatar_preview" src="{{ asset(Auth::user()->profile_pic) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <span id="profile_avatar_initial" class="text-white font-black text-2xl sm:text-3xl">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-sm">
                                <i class="fas fa-camera"></i>
                            </div>
                            <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-indofood-blue text-white text-xs flex items-center justify-center ring-2 ring-white shadow-sm">
                                <i class="fas fa-pen text-[9px]"></i>
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800">Foto Profil</h4>
                            <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate" id="profile_pic_filename">Format JPG, PNG, WEBP (Maks. 2MB)</p>
                            <div class="mt-2.5 flex items-center gap-2.5">
                                <button type="button" onclick="document.getElementById('profile_pic_input').click()" class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-blue-500 hover:text-blue-700 text-slate-700 text-xs font-semibold transition-all shadow-2xs cursor-pointer">
                                    Pilih Foto
                                </button>
                                <button type="button" id="btn_cancel_photo" onclick="cancelPhotoSelection()" class="hidden text-xs text-red-500 hover:text-red-700 font-semibold transition cursor-pointer">
                                    Batalkan
                                </button>
                            </div>
                            <input type="file" id="profile_pic_input" name="profile_pic" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewProfileImage(this)">
                        </div>
                    </div>
                </div>
                @error('profile_pic')
                    <p class="text-xs text-red-600 -mt-3 flex items-center gap-1.5 font-medium"><i class="fas fa-circle-exclamation text-xs"></i> {{ $message }}</p>
                @enderror

                {{-- Nama Lengkap --}}
                <div class="space-y-1.5">
                    <label for="profile_input_name" class="block text-xs font-bold text-slate-700">
                        Nama Lengkap
                    </label>
                    <div class="relative flex items-center">
                        <i class="fas fa-user text-slate-400 text-xs absolute left-4 pointer-events-none"></i>
                        <input type="text" id="profile_input_name" name="name" value="{{ ucwords(strtolower(Auth::user()->name)) }}" required class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-50/80 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all font-medium h-11 sm:h-12 shadow-2xs">
                    </div>
                    @error('name')
                        <p class="text-xs text-red-600 mt-1 flex items-center gap-1.5 font-medium"><i class="fas fa-circle-exclamation text-xs"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="profile_input_email" class="block text-xs font-bold text-slate-700">
                        Alamat Email
                    </label>
                    <div class="relative flex items-center">
                        <i class="fas fa-envelope text-slate-400 text-xs absolute left-4 pointer-events-none"></i>
                        <input type="email" id="profile_input_email" name="email" value="{{ Auth::user()->email }}" required class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-50/80 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all font-medium h-11 sm:h-12 shadow-2xs">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-1 flex items-center gap-1.5 font-medium"><i class="fas fa-circle-exclamation text-xs"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Role Akses (Terkunci) Card --}}
                <div class="p-4 sm:p-4.5 rounded-2xl bg-slate-50/90 border border-slate-200/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100/70 text-blue-700 flex items-center justify-center text-sm shrink-0">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-slate-700 leading-tight">Hak Akses (Role)</p>
                            <p class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-lock text-[10px]"></i> Terkunci oleh sistem
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider bg-white text-blue-800 border border-blue-200/80 shadow-2xs shrink-0">
                        {{ strtoupper(Auth::user()->role ?? 'ADMIN') }}
                    </span>
                </div>

                {{-- Section Ganti Password --}}
                <div class="pt-5 mt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-key text-blue-600 text-xs"></i> Ganti Kata Sandi
                        </span>
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Opsional</span>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="profile_password" class="block text-xs font-semibold text-slate-700">Password Baru</label>
                                <span class="text-[10px] text-slate-400">kosongkan jika tidak diubah</span>
                            </div>
                            <div class="relative flex items-center">
                                <i class="fas fa-lock text-slate-400 text-xs absolute left-4 pointer-events-none"></i>
                                <input type="password" id="profile_password" name="password" minlength="8" placeholder="Minimal 8 karakter" class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-50/80 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all font-medium h-11 sm:h-12 shadow-2xs">
                                <button type="button" onclick="togglePasswordVisibilityProfile('profile_password', this)" class="absolute right-3.5 text-slate-400 hover:text-blue-600 cursor-pointer focus:outline-none p-1" title="Lihat/Sembunyikan">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-600 mt-1 flex items-center gap-1.5 font-medium"><i class="fas fa-circle-exclamation text-xs"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="profile_password_confirmation" class="block text-xs font-semibold text-slate-700">Konfirmasi Password Baru</label>
                            <div class="relative flex items-center">
                                <i class="fas fa-lock-open text-slate-400 text-xs absolute left-4 pointer-events-none"></i>
                                <input type="password" id="profile_password_confirmation" name="password_confirmation" minlength="8" placeholder="Ketik ulang password baru" class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-50/80 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all font-medium h-11 sm:h-12 shadow-2xs">
                                <button type="button" onclick="togglePasswordVisibilityProfile('profile_password_confirmation', this)" class="absolute right-3.5 text-slate-400 hover:text-blue-600 cursor-pointer focus:outline-none p-1" title="Lihat/Sembunyikan">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Actions (Always Pinned at Bottom) --}}
            <div class="px-5 sm:px-7 py-3.5 sm:py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0" style="flex-shrink: 0;">
                <button type="button" onclick="closeEditProfileModal()" class="px-4 sm:px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-200/60 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 sm:px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indofood-blue hover:bg-blue-800 shadow-md shadow-blue-900/20 active:scale-[0.98] transition-all cursor-pointer flex items-center gap-2" style="background-color: #004a99 !important;">
                    <i class="fas fa-floppy-disk text-xs"></i> Simpan Data
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    /* Styling scrollbar halus dan jelas untuk modal */
    .modal-scrollable-body::-webkit-scrollbar {
        width: 6px;
    }
    .modal-scrollable-body::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 9999px;
    }
    .modal-scrollable-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .modal-scrollable-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .modal-scrollable-body {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
</style>

<script>
    const originalAvatarHtml = document.getElementById('profile_avatar_wrapper') ? document.getElementById('profile_avatar_wrapper').innerHTML : '';

    function previewProfileImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const wrapper = document.getElementById('profile_avatar_wrapper');
                if (wrapper) {
                    wrapper.innerHTML = `<img id="profile_avatar_preview" src="${e.target.result}" alt="Preview Avatar" class="w-full h-full object-cover">`;
                }
                const filenameLabel = document.getElementById('profile_pic_filename');
                if (filenameLabel) {
                    filenameLabel.innerText = file.name;
                    filenameLabel.classList.add('text-blue-600', 'font-semibold');
                }
                const btnCancel = document.getElementById('btn_cancel_photo');
                if (btnCancel) {
                    btnCancel.classList.remove('hidden');
                }
            };

            reader.readAsDataURL(file);
        }
    }

    function cancelPhotoSelection() {
        const input = document.getElementById('profile_pic_input');
        if (input) input.value = '';

        const wrapper = document.getElementById('profile_avatar_wrapper');
        if (wrapper && originalAvatarHtml) {
            wrapper.innerHTML = originalAvatarHtml;
        }

        const filenameLabel = document.getElementById('profile_pic_filename');
        if (filenameLabel) {
            filenameLabel.innerText = 'JPG, PNG, WEBP (Maks. 2MB)';
            filenameLabel.classList.remove('text-blue-600', 'font-semibold');
        }

        const btnCancel = document.getElementById('btn_cancel_photo');
        if (btnCancel) {
            btnCancel.classList.add('hidden');
        }
    }

    function togglePasswordVisibilityProfile(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn ? btn.querySelector('i') : null;
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

    window.openEditProfileModal = function() {
        const modal = document.getElementById('editProfileModal');
        if (!modal) return;
        modal.classList.remove('opacity-0', 'pointer-events-none');
        if (modal.children && modal.children[0]) {
            modal.children[0].classList.remove('scale-95');
        }
        document.body.style.overflow = 'hidden';
    };

    window.closeEditProfileModal = function() {
        const modal = document.getElementById('editProfileModal');
        if (!modal) return;
        modal.classList.add('opacity-0', 'pointer-events-none');
        if (modal.children && modal.children[0]) {
            modal.children[0].classList.add('scale-95');
        }
        document.body.style.overflow = '';
    };

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditProfileModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('editProfileModal');
                if (modal && !modal.classList.contains('opacity-0')) {
                    closeEditProfileModal();
                }
            }
        });
    });
</script>
