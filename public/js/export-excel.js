

function applyFullBorder(worksheet) {

    worksheet.eachRow({ includeEmpty: false }, function(row, rowNumber) {

        row.eachCell({ includeEmpty: false }, function(cell, colNumber) {

            cell.border = {

                top: { style: 'thin' },

                left: { style: 'thin' },

                bottom: { style: 'thin' },

                right: { style: 'thin' }

            };

            cell.alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };

        });

    });

}

window.downloadExcelLaporan = function() {

    if (!window.masterData || window.masterData.length === 0) {

        Swal.fire('Data Kosong', 'Tidak ada data master untuk diexport.', 'warning');

        return;

    }

    const workbook = new ExcelJS.Workbook();

    const worksheet = workbook.addWorksheet('Master Stok');

    worksheet.columns = [

        { header: 'No', key: 'no', width: 5 },

        { header: 'Tanggal', key: 'tanggal', width: 15 },

        { header: 'Kode', key: 'kode', width: 15 },

        { header: 'Nama Barang', key: 'nama_barang', width: 35 },

        { header: 'Kategori', key: 'kategori', width: 20 },

        { header: 'Satuan', key: 'satuan', width: 10 },

        { header: 'Stok Awal', key: 'stok_awal', width: 12 },

        { header: 'Masuk', key: 'stok_masuk', width: 10 },

        { header: 'Keluar', key: 'stok_keluar', width: 10 },

        { header: 'Rata2 Pakai/Hari', key: 'rata_penggunaan', width: 18 },

        { header: 'Pemakaian Terbanyak', key: 'pemakaian_terbanyak', width: 25 },

        { header: 'Lead Time', key: 'lead_time', width: 12 },

        { header: 'Safety Stok', key: 'safety_stock', width: 12 },

        { header: 'Ending Stock', key: 'total_stok', width: 15 },

        { header: 'Status Reorder', key: 'status_reorder', width: 18 },

        { header: 'Harga Unit', key: 'harga_unit', width: 15 }

    ];

    worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };

    worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF004A99' } };

    worksheet.getRow(1).alignment = { vertical: 'middle', horizontal: 'center' };

    window.masterData.forEach((item, index) => {

        let tglStr = item.updated_at ? new Date(item.updated_at).toISOString().split('T')[0] : '';

        worksheet.addRow({

            no: index + 1,

            tanggal: tglStr,

            kode: item.kode,

            nama_barang: item.nama_barang,

            kategori: item.kategori,

            satuan: item.satuan,

            stok_awal: item.stok_awal,

            stok_masuk: item.stok_masuk,

            stok_keluar: item.stok_keluar,

            rata_penggunaan: item.rata_penggunaan,

            pemakaian_terbanyak: item.pemakaian_terbanyak,

            lead_time: item.lead_time,

            safety_stock: item.safety_stock,

            total_stok: item.total_stok,

            status_reorder: item.status_reorder,

            harga_unit: item.harga_unit

        });

    });

    applyFullBorder(worksheet);

    workbook.xlsx.writeBuffer().then(function(buffer) {

        saveAs(new Blob([buffer]), "Laporan_Master_Stationery.xlsx");

    });

};

window.downloadExcelPengadaan = function() {

    if (!window.dataPengadaanRaw || window.dataPengadaanRaw.length === 0) {

        Swal.fire('Data Kosong', 'Tidak ada data pengadaan untuk diexport.', 'warning');

        return;

    }

    const workbook = new ExcelJS.Workbook();

    const worksheet = workbook.addWorksheet('Data Pengadaan');

    worksheet.columns = [

        { header: 'No', key: 'no', width: 5 },

        { header: 'Tgl PR', key: 'tgl_pr', width: 15 },

        { header: 'No. PR', key: 'no_pr', width: 15 },

        { header: 'Kode Barang', key: 'kode_barang', width: 15 },

        { header: 'Nama Barang', key: 'nama_barang', width: 30 },

        { header: 'Qty', key: 'qty_item', width: 10 },

        { header: 'No. PO', key: 'no_po', width: 15 },

        { header: 'Vendor', key: 'vendor', width: 25 },

        { header: 'No. Dokumen', key: 'no_dokumen', width: 20 },

        { header: 'Status', key: 'status', width: 15 },

        { header: 'Estimasi Tiba', key: 'estimasi_tiba', width: 15 }

    ];

    worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };

    worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF004A99' } };

    worksheet.getRow(1).alignment = { vertical: 'middle', horizontal: 'center' };

    window.dataPengadaanRaw.forEach((p, index) => {

        let tglPrStr = p.tgl_pr ? p.tgl_pr.split(' ')[0] : '';

        let estimasiStr = p.estimasi_tiba ? p.estimasi_tiba.split(' ')[0] : '-';

        let kode = p.stationery ? p.stationery.kode : 'N/A';

        let nama = p.stationery ? p.stationery.nama_barang : 'Barang Dihapus';

        worksheet.addRow({

            no: index + 1,

            tgl_pr: tglPrStr,

            no_pr: p.no_pr,

            kode_barang: kode,

            nama_barang: nama,

            qty_item: p.qty_item,

            no_po: p.no_po || '-',

            vendor: p.vendor || '-',

            no_dokumen: p.no_dokumen || '-',

            status: p.status,

            estimasi_tiba: estimasiStr

        });

    });

    applyFullBorder(worksheet);

    workbook.xlsx.writeBuffer().then(function(buffer) {

        saveAs(new Blob([buffer]), "Data_Pengadaan_PO_PR.xlsx");

    });

};

window.exportLaporanData = function(type) {

    if (type === 'order') {

        window.downloadExcelLaporan();

    } else if (type === 'pemakaian') {

        let dataPemakaian = window.rawTrxData.filter(trx => trx.type && trx.type.toLowerCase() === 'keluar');

        if (dataPemakaian.length === 0) {

            Swal.fire('Data Kosong', 'Tidak ada riwayat pemakaian barang.', 'warning');

            return;

        }

        const workbook = new ExcelJS.Workbook();

        const worksheet = workbook.addWorksheet('Riwayat Pemakaian');

        worksheet.columns = [

            { header: 'No', key: 'no', width: 5 },

            { header: 'Tgl Keluar', key: 'tanggal', width: 15 },

            { header: 'No FPB / PR', key: 'no_fpb', width: 20 },

            { header: 'Kode Barang', key: 'kode', width: 15 },

            { header: 'Nama Barang', key: 'nama_barang', width: 35 },

            { header: 'Departemen (Pemakai)', key: 'dept', width: 25 },

            { header: 'Jumlah Dipakai', key: 'jumlah', width: 15 },

            { header: 'Satuan', key: 'satuan', width: 10 },

            { header: 'Keterangan', key: 'keterangan', width: 30 }

        ];

        worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };

        worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFDC2626' } };

        worksheet.getRow(1).alignment = { vertical: 'middle', horizontal: 'center' };

        dataPemakaian.forEach((trx, index) => {

            let tglStr = trx.tanggal ? trx.tanggal.split(' ')[0] : '';

            let fpb = trx.no_fpb || 'PR-' + trx.id;

            let kode = trx.stationery ? trx.stationery.kode : '-';

            let nama = trx.stationery ? trx.stationery.nama_barang : '-';

            let satuan = trx.stationery ? trx.stationery.satuan : '-';

            worksheet.addRow({

                no: index + 1,

                tanggal: tglStr,

                no_fpb: fpb,

                kode: kode,

                nama_barang: nama,

                dept: trx.dept || '-',

                jumlah: trx.jumlah,

                satuan: satuan,

                keterangan: trx.keterangan || '-'

            });

        });

        applyFullBorder(worksheet);

        workbook.xlsx.writeBuffer().then(function(buffer) {

            saveAs(new Blob([buffer]), "Riwayat_Pemakaian_Barang.xlsx");

        });

    }

};
