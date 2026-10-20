# TODO: Tambahkan Full Border di Stationery Excel Export

## Info yang Sudah Didapat:
- File target: `resources/views/Dashboard/stationery.blade.php`
- 3 fungsi export Excel perlu dimodifikasi:
  1. `async function downloadExcelLaporan()` - multi-worksheet (ws1, ws2, ws3)
  2. `window.exportLaporanData = async function(tipe)` - single worksheet (ws)
  3. `window.downloadExcelPengadaan = async function()` - single worksheet (ws)

## Langkah-langkah:

### LANGKAH 1: Tambahkan Helper applyFullBorder
Tambahkan fungsi ini di dalam tag `<script>` ( di atas/permulaan script )

### LANGKAH 2: Modifikasi downloadExcelLaporan()
Tambahkan `applyFullBorder(ws1); applyFullBorder(ws2); applyFullBorder(ws3);` sebelum `const buffer = await wb.xlsx.writeBuffer();`

### LANGKAH 3: Modifikasi exportLaporanData()
Tambahkan `applyFullBorder(ws);` sebelum `const buffer = await wb.xlsx.writeBuffer();`

### LANGKAH 4: Modifikasi downloadExcelPengadaan()
Tambahkan `applyFullBorder(ws);` sebelum `const buffer = await wb.xlsx.writeBuffer();`

## Status: MENUNGO KONFIRMASI USER
