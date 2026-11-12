{{-- MODAL EDIT PEMAKAIAN BARANG --}}
<div id="modalEditPemakaian" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden transform scale-100 border-t-4 border-indigo-600 flex flex-col max-h-[90vh]">

        <div class="bg-white px-8 pt-5 pb-4 border-b border-gray-200 flex flex-col shrink-0 gap-4">
            <div class="flex justify-between items-center w-full">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 text-indigo-600 p-3 rounded-xl shadow-inner"><i class="fas fa-edit text-2xl"></i></div>
                    <div><h2 class="font-black text-lg text-gray-800 uppercase tracking-widest">Edit Pemakaian Barang</h2><p class="text-[11px] text-gray-500 mt-1 font-bold tracking-wide">Update riwayat pengambilan material</p></div>
                </div>
                <button type="button" onclick="document.getElementById('modalEditPemakaian').classList.add('hidden')" class="text-gray-400 hover:text-red-600 bg-gray-100 hover:bg-red-50 border border-gray-200 rounded-xl p-2.5 transition-colors shadow-sm"><i class="fas fa-times text-lg"></i></button>
            </div>
        </div>

        <form id="formEditPemakaian" method="POST" class="p-8 space-y-6 overflow-y-auto bg-gray-50 flex-1">
            @csrf @method('PUT')
            <input type="hidden" name="type" value="keluar">

            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="stationery_id" id="edit_out_stationery_id" required class="w-full bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:border-indigo-500 outline-none select2-no-icon">
                    <option value="">{{-- Data akan diisi JS --}}</option>
                    @foreach($dataStationery->sortBy('nama_barang') as $item)
                        @if(!isset($item->is_active) || $item->is_active == 1 || $item->is_active === true || $item->is_active === 'Aktif')
                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="flex gap-6">
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_out_tanggal" required class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" id="edit_out_jumlah" required min="1" class="w-full py-3 px-4 bg-white border-2 border-red-300 rounded-xl text-lg font-black text-red-600">
                </div>
            </div>

            <div class="flex gap-6">
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">No FPB</label>
                    <input type="text" name="no_fpb" id="edit_out_no_fpb" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Departemen</label>
                    <input type="text" name="dept" id="edit_out_dept" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Keterangan / Keperluan</label>
                <textarea name="keterangan" id="edit_out_keterangan" rows="3" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800 resize-vertical"></textarea>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl bg-indigo-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-indigo-700 transition">UPDATE PEMAKAIAN</button>
        </form>
    </div>
</div>
