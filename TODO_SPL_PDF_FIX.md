# TODO_SPL_PDF_FIX

- [x] Identifikasi error: View [pdf.spl] not found saat akses GET /spl/1
- [x] Cek controller SplController: show()/download() me-render view `pdf.spl`
- [x] Cek resources/views/pdf: hanya ada `jadwal_karyawan.blade.php`, file `spl.blade.php` tidak ada
- [x] Tambahkan `resources/views/pdf/spl.blade.php` template resmi lengkap (kolom tabel lengkap & 4 kolom TTD persetujuan)
- [x] Perbaiki bug input hidden total_jam terhapus di DOM pengajuan_spl.blade.php
- [x] Tambahkan migrasi kolom tanda tangan (dibuat_oleh, ditugaskan_oleh, disetujui_oleh, diketahui_oleh) pada tabel spls
- [x] Perbaiki class facade Barryvdh\DomPDF\Facade\Pdf pada SplController
- [x] Test render & download PDF SPL orientation A4 landscape (Berhasil)

