<div id="editProfileModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-all duration-300 border border-slate-100 flex flex-col max-h-[90vh]">

        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
            <h3 class="text-[12px] font-black text-slate-700 uppercase tracking-widest flex items-center">
                <i class="fas fa-user-edit mr-2.5 text-blue-800 text-base"></i> Edit Profil Saya
            </h3>
            <button type="button" onclick="closeEditProfileModal()" class="text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors w-8 h-8 rounded-full flex items-center justify-center">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest ml-1 block mb-1.5">Upload Foto Baru (Opsional)</label>
                    <input type="file" name="profile_pic" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('profile_pic')
                        <p class="text-[10px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest ml-1 block mb-1.5">Nama Lengkap</label>
                    {{-- Format nama disamakan dengan header (Title Case) --}}
                    <input type="text" name="name" value="{{ ucwords(strtolower(Auth::user()->name)) }}" required class="form-input-modern text-slate-800 text-sm py-2.5">
                </div>

                <div>
                    <label class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest ml-1 block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" required class="form-input-modern text-slate-800 text-sm py-2.5">
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mt-2 shadow-inner">
                    <label class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block mb-1.5">Role Akses (Terkunci)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 text-xs"></i>
                        </div>
                        <input type="text" value="{{ strtoupper(Auth::user()->role ?? 'ADMIN') }}" readonly class="w-full bg-slate-200 border-none rounded-lg py-2.5 pl-8 pr-3 text-sm font-black text-slate-600 cursor-not-allowed focus:outline-none shadow-sm">
                    </div>
                    <p class="text-[9px] text-slate-400 mt-2 font-medium italic leading-relaxed">*Hubungi administrator utama jika Anda perlu mengubah hak akses role sistem.</p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-3 flex items-center">
                        <i class="fas fa-key mr-2 text-slate-400"></i> Ganti Password <span class="ml-1 text-slate-400 font-medium">(Opsional)</span>
                    </h4>

                    <div class="space-y-3">
                        <div>
                            <label class="text-[9px] font-extrabold text-slate-500 uppercase tracking-widest ml-1 block mb-1">Password Baru</label>
                            <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah" class="form-input-modern text-slate-800 text-sm py-2">
                        </div>
                        <div>
                            <label class="text-[9px] font-extrabold text-slate-500 uppercase tracking-widest ml-1 block mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru" class="form-input-modern text-slate-800 text-sm py-2">
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6 shrink-0">
                    <button type="button" onclick="closeEditProfileModal()" class="px-5 py-2.5 rounded-xl text-[10px] font-extrabold uppercase tracking-widest bg-white border-2 border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-[10px] font-extrabold uppercase tracking-widest bg-blue-800 text-white hover:bg-blue-900 transition shadow-lg shadow-blue-800/30 flex items-center gap-2">
                        <i class="fas fa-save text-sm"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
