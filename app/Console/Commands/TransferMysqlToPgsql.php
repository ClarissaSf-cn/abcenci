<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class TransferMysqlToPgsql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neon:transfer-data 
                            {--truncate : Hapus data yang ada di database target PostgreSQL sebelum transfer}
                            {--migrate : Jalankan php artisan migrate pada database PostgreSQL terlebih dahulu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer semua data dari MySQL lokal ke PostgreSQL Neon DB secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== INISIALISASI TRANSFER DATA KE NEON DB (POSTGRESQL) ===");

        // 1. Cek Koneksi MySQL (Source)
        try {
            DB::connection('mysql')->getPdo();
            $this->info("✓ Terhubung ke sumber database (MySQL Lokal).");
        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke MySQL: " . $e->getMessage());
            return 1;
        }

        // 2. Cek Koneksi PostgreSQL (Destination - Unpooled untuk migrasi & DDL)
        $targetConnection = config('database.connections.pgsql_unpooled') ? 'pgsql_unpooled' : 'pgsql';
        try {
            DB::connection($targetConnection)->getPdo();
            $this->info("✓ Terhubung ke target database (PostgreSQL / Neon DB via {$targetConnection}).");
        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke Neon PostgreSQL: " . $e->getMessage());
            $this->warn("Pastikan DATABASE_URL / konfigurasi PostgreSQL di .env sudah benar dan ekstensi pdo_pgsql aktif.");
            return 1;
        }

        // 3. Jalankan Migrasi di PostgreSQL jika diminta
        if ($this->option('migrate')) {
            $this->info("Menjalankan migrasi struktur tabel pada PostgreSQL (direct connection)...");
            Artisan::call('migrate', ['--database' => $targetConnection, '--force' => true]);
            $this->line(Artisan::output());
        }

        // Daftar tabel aplikasi yang akan dipindahkan secara terurut (memperhatikan relasi)
        $tables = [
            'users',
            'employees',
            'shifts',
            'attendance_locations',
            'attendances',
            'spls',
            'spl_details',
            'stationeries',
            'stationery_transactions',
            'pengadaans',
            'penilaians',
            'closing_periodes',
            'employee_schedules',
            'lembur'
        ];

        // Mulai proses transfer data
        $totalRecordsTransferred = 0;

        foreach ($tables as $table) {
            if (!Schema::connection('mysql')->hasTable($table)) {
                $this->warn("Tabel [{$table}] tidak ditemukan di MySQL. Dilewati.");
                continue;
            }

            if (!Schema::connection($targetConnection)->hasTable($table)) {
                $this->warn("Tabel [{$table}] belum ada di PostgreSQL. Jalankan migrasi terlebih dahulu.");
                continue;
            }

            $countSource = DB::connection('mysql')->table($table)->count();
            if ($countSource === 0) {
                $this->line("Tabel [{$table}]: Kosong di MySQL (0 baris).");
                continue;
            }

            // Bersihkan data awal (misal dari initial seeder migration) agar tidak duplicate key
            DB::connection($targetConnection)->table($table)->delete();

            $this->info("Mentransfer tabel [{$table}] ({$countSource} baris)...");
            $bar = $this->output->createProgressBar($countSource);
            $bar->start();

            DB::connection('mysql')->table($table)->orderBy('id')->chunk(200, function ($rows) use ($table, $targetConnection, &$totalRecordsTransferred, $bar) {
                $data = [];
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $data[] = $rowArray;
                }

                if (!empty($data)) {
                    DB::connection($targetConnection)->table($table)->insert($data);
                    $totalRecordsTransferred += count($data);
                    $bar->advance(count($data));
                }
            });

            $bar->finish();
            $this->newLine();

            // Reset Sequence Auto-Increment di PostgreSQL agar INSERT baru tidak error duplicate key
            try {
                DB::connection($targetConnection)->statement("
                    SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE(max(id), 1), max(id) IS NOT NULL) FROM \"{$table}\";
                ");
            } catch (\Exception $seqEx) {
                // Lewati jika tabel tidak menggunakan primary key sequence 'id'
            }
        }

        $this->newLine();
        $this->info("✓ BERHASIL: Sebanyak {$totalRecordsTransferred} baris data telah dipindahkan ke Neon DB!");
        $this->info("Tips: Setelah migrasi selesai, ubah DB_CONNECTION=pgsql di file .env Anda.");

        return 0;
    }
}

