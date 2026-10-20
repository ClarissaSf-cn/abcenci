<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('jabatan')->nullable();
            $table->string('employee_group')->nullable();
            $table->string('pers_subarea_text')->nullable();
            $table->string('sub_subarea')->nullable();
            $table->date('tmk')->nullable();
            $table->string('length_of_service')->nullable();

            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('age')->nullable();
            $table->string('range_umur')->nullable();
            $table->string('religion')->nullable();

            $table->string('jamsostek_id')->nullable();
            $table->string('bpjs_id')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('tax_status')->nullable();
            $table->integer('number_of_child')->nullable();
            $table->string('ktp_nomor')->nullable();
            $table->text('ktp_alamat')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            $table->string('esgrp_text')->nullable();
            $table->string('level')->nullable();
            $table->string('position_id')->nullable();
            $table->string('org_unit_id')->nullable();
            $table->string('org_unit_text')->nullable();
            $table->string('pers_subarea_id')->nullable();
            $table->string('org_key_id')->nullable();
            $table->string('org_key_text')->nullable();

            $table->string('bantu_opr_hlp')->nullable();
            $table->string('regu_line')->nullable();
            $table->string('line')->nullable();
            $table->string('regu')->nullable();
            $table->string('area_prov_marketing')->nullable();
            $table->string('distrik_rayon_marketing')->nullable();
            $table->string('vendor_os')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('cost_center_text')->nullable();

            $table->string('emp_status_text')->default('Active');
            $table->string('excuse')->nullable();
            $table->date('tanggal_akhir_kerja')->nullable();
            $table->string('pendidikan_level')->nullable();
            $table->string('pendidikan_institusi')->nullable();
            $table->string('pendidikan_studi')->nullable();
            $table->string('ps_type_text')->nullable();
            $table->string('status_incentive')->nullable();

            $table->string('bank_keys')->nullable();
            $table->string('bank_account_no')->nullable();

            $table->date('vaksin1_tanggal')->nullable();
            $table->string('vaksin1_merk')->nullable();
            $table->date('vaksin2_tanggal')->nullable();
            $table->string('vaksin2_merk')->nullable();
            $table->date('vaksin3_tanggal')->nullable();
            $table->string('vaksin3_merk')->nullable();

            $table->string('workschedule_id')->nullable();
            $table->string('tipe_hari_kerja')->nullable();
            $table->string('shift_type')->nullable();
            $table->string('no_sidik_jari')->nullable();
            $table->string('jam_kerja')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
