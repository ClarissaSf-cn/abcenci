# TODO: Pagination Master Shift

## Step 1
- [x] Baca `resources/views/Dashboard/master_shift.blade.php` untuk lihat struktur tabel.
- [x] Baca `app/Http/Controllers/ShiftController.php` (cek bahwa daftar master shift tidak ada di sini).

## Step 2 (Controller supply data)
- [x] Ubah penyedia data `$shifts` agar jadi paginated dari `AttendanceController@index`.
  - `Shift::orderBy('wst_code', 'asc')->paginate(15)->withQueryString();`
- [x] Pastikan juga di `resources/views/Dashboard/dashboard.blade.php` `$shifts` paginated.

## Step 3 (Blade UI)
- [x] Update `master_shift.blade.php`:
  - offset nomor baris pakai `$offset + $index + 1`
  - tampilkan paginator: `{{ $shifts->links() }}`

## Step 4 (Cache/views)
- [x] Jalankan `php artisan view:clear`

## Step 5 (Validasi manual - checklist)
- [ ] Buka tab `Master Shift` lalu coba klik page 2/3.
- [ ] Pastikan tidak berubah ke halaman/tab lain (hash tetap `#master-shift`).
- [ ] Pastikan nomor urut tetap berlanjut.
- [ ] Pastikan filter/search JS tetap bekerja (di halaman yang tampil).
- [ ] Pastikan edit/hapus tetap berfungsi.

