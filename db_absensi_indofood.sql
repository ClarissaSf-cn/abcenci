-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 20, 2026 at 04:58 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi_indofood`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `jam_masuk` datetime DEFAULT NULL,
  `jam_assorting` datetime DEFAULT NULL,
  `jam_masuk_lembur` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hadir',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_karyawan_assorting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `user_id`, `jam_masuk`, `jam_assorting`, `jam_masuk_lembur`, `status`, `foto`, `keterangan`, `catatan`, `nama_karyawan_assorting`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1791269952_2452.jpg', NULL, 'Assorting: 80027016 - Muhammad Jerry Noval Ramadhan | Shift:- | M:14:59 [TELAT] | S:- | Ket: ea [GPS: -3.54989, 114.7314 | Jarak: 205m]', NULL, '2026-10-06 06:59:13', '2026-10-05 22:59:13'),
(2, 6, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1791276318_2459.jpg', NULL, 'Assorting: 80007045 - Mujairimi | Shift:- | M:16:45 [TELAT] | S:- | Ket: - [GPS: -3.54989, 114.7314 | Jarak: 205m]', NULL, '2026-10-06 08:45:19', '2026-10-06 00:45:19'),
(3, 6, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1791528214_4608.jpg', NULL, 'Assorting: 80069724 - Muhammad Syafii | Shift:- | M:14:43 [TELAT] | S:- | Ket: - [GPS: -3.54974, 114.73084 | Jarak: 145m]', NULL, '2026-10-09 06:43:36', '2026-10-08 22:43:36'),
(4, 6, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1791528307_4960.jpg', NULL, 'Assorting: 80007045 - Mujairimi | Shift:- | M:14:45 [TELAT] | S:- | Ket: - [GPS: -3.54974, 114.73084 | Jarak: 145m]', NULL, '2026-10-09 06:45:07', '2026-10-08 22:45:07'),
(6, 6, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1792052427_3915.jpg', NULL, 'Assorting: 80007045 - Mujairimi | Shift:- | M:16:20 [TELAT] | S:- | Ket: - [GPS: -3.54989, 114.7314 | Jarak: 205m]', NULL, '2026-10-15 08:20:27', '2026-10-15 00:20:27'),
(7, 6, NULL, NULL, NULL, 'Hadir', 'absensi/absen_1792116741_3791.jpg', NULL, 'Assorting: 80007045 - Mujairimi | Shift:- | M:10:12 [TELAT] | S:- | Ket: lapar [GPS: -3.54989, 114.7314 | Jarak: 205m]', NULL, '2026-10-16 02:12:24', '2026-10-15 18:12:24'),
(9, 1, '2026-10-16 13:41:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80045719 - Abdul Azis | Shift:- | M:13:41 | S:15:45 | Akm: 2.6 | Ket: Lembur | ~T:2026-10-16|I:30|R:1.57|15:1.00|2:0.57|3:0.00|4:0.00~', NULL, '2026-10-16 03:41:31', '2026-10-16 03:41:31'),
(10, 1, '2026-10-16 15:22:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80045719 - Abdul Azis | Shift:- | M:15:22 | S:17:22 | Akm: 3.5 | Ket: Lembur | ~T:2026-10-16|I:0|R:2.00|15:1.00|2:1.00|3:0.00|4:0.00~', NULL, '2026-10-16 05:22:17', '2026-10-16 05:22:17'),
(11, 1, '2026-10-16 15:22:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80045719 - Abdul Azis | Shift:- | M:15:22 | S:17:22 | Akm: 3.5 | Ket: Lembur | ~T:2026-10-16|I:0|R:2.00|15:1.00|2:1.00|3:0.00|4:0.00~', NULL, '2026-10-16 05:22:28', '2026-10-16 05:22:28'),
(12, 1, '2026-10-16 15:22:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80045719 - Abdul Azis | Shift:- | M:15:22 | S:17:22 | Akm: 3.5 | Ket: Lembur | ~T:2026-10-16|I:0|R:2.00|15:1.00|2:1.00|3:0.00|4:0.00~', NULL, '2026-10-16 05:22:28', '2026-10-16 05:22:28'),
(13, 1, '2026-10-16 15:22:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80045719 - Abdul Azis | Shift:- | M:15:22 | S:17:22 | Akm: 3.5 | Ket: Lembur | ~T:2026-10-16|I:0|R:2.00|15:1.00|2:1.00|3:0.00|4:0.00~', NULL, '2026-10-16 05:22:28', '2026-10-16 05:22:28'),
(14, 1, '2026-10-16 14:23:00', NULL, NULL, 'Lembur', NULL, NULL, 'Assorting: 80008139 - Abdul Solihin | Shift:- | M:14:23 | S:16:23 | Akm: 3.5 | Ket: Lembur | ~T:2026-10-16|I:0|R:2.00|15:1.00|2:1.00|3:0.00|4:0.00~', NULL, '2026-10-16 05:23:11', '2026-10-16 05:23:11'),
(15, 6, NULL, NULL, NULL, 'Pulang', 'absensi/absen_1792138105_3883.jpg', NULL, 'Assorting: 80007045 - Mujairimi | Shift:- | M:- | S:16:08 | Ket: - [GPS: -3.54989, 114.7314 | Jarak: 205m]', NULL, '2026-10-16 08:08:27', '2026-10-16 00:08:27');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `closing_periodes`
--

CREATE TABLE `closing_periodes` (
  `id` bigint UNSIGNED NOT NULL,
  `bulan` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pers_subarea_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_subarea` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmk` date DEFAULT NULL,
  `length_of_service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthplace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `age` int DEFAULT NULL,
  `range_umur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jamsostek_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bpjs_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_of_child` int DEFAULT NULL,
  `ktp_nomor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ktp_alamat` text COLLATE utf8mb4_unicode_ci,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `esgrp_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_unit_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_unit_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pers_subarea_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_key_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_key_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bantu_opr_hlp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regu_line` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_prov_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrik_rayon_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_os` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_center_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_status_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `excuse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_akhir_kerja` date DEFAULT NULL,
  `pendidikan_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_institusi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_studi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ps_type_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_incentive` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_keys` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaksin1_tanggal` date DEFAULT NULL,
  `vaksin1_merk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaksin2_tanggal` date DEFAULT NULL,
  `vaksin2_merk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaksin3_tanggal` date DEFAULT NULL,
  `vaksin3_merk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workschedule_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_hari_kerja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_sidik_jari` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_kerja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `nik`, `nama`, `user`, `jumlah`, `jabatan`, `employee_group`, `pers_subarea_text`, `sub_subarea`, `tmk`, `length_of_service`, `gender`, `marital_status`, `birthplace`, `birthdate`, `age`, `range_umur`, `religion`, `jamsostek_id`, `bpjs_id`, `tax_id`, `tax_status`, `number_of_child`, `ktp_nomor`, `ktp_alamat`, `region`, `postal_code`, `no_hp`, `email`, `esgrp_text`, `level`, `position_id`, `org_unit_id`, `org_unit_text`, `pers_subarea_id`, `org_key_id`, `org_key_text`, `bantu_opr_hlp`, `regu_line`, `line`, `regu`, `area_prov_marketing`, `distrik_rayon_marketing`, `vendor_os`, `cost_center`, `cost_center_text`, `emp_status_text`, `excuse`, `tanggal_akhir_kerja`, `pendidikan_level`, `pendidikan_institusi`, `pendidikan_studi`, `ps_type_text`, `status_incentive`, `bank_keys`, `bank_account_no`, `vaksin1_tanggal`, `vaksin1_merk`, `vaksin2_tanggal`, `vaksin2_merk`, `vaksin3_tanggal`, `vaksin3_merk`, `workschedule_id`, `tipe_hari_kerja`, `shift_type`, `no_sidik_jari`, `jam_kerja`, `created_at`, `updated_at`) VALUES
(1, '80007039', 'Janie', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2011-12-07', '14 y 07 m', 'Male', 'Marr.', 'Pandahan', '1979-10-21', 46, '46-55', 'Moslem', '11039180887', '0001143302354', '-', '-', 0, '6301052110790002', 'NUSA INDAH RT 009 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMP', 'Madrasah Tsanawiyah Pandahan', 'Umum', 'Operative (NI)', 'Non Inc', '-', '9000011252013', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007039', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(2, '80007041', 'Joni', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2011-04-01', '15 y 03 m', 'Male', 'Marr.', 'Cirebon', '1972-09-02', 53, '46-55', 'Moslem', '08019364721', '0001134330456', '-', '-', 0, '6301050209720001', 'JL. A. YANI UJUNG RT 004 RW 001 - BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMP', 'SMP PGRI Dukuti Bumang', 'Umum', 'Operative (NI)', 'Non Inc', '-', '9000011252120', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007041', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(3, '80007042', 'Muhammad Aini', 'GAS Spv', 1, 'Team Leader', 'Outsourcing', 'ADM HR', 'Non Produksi', '2008-04-07', '18 y 03 m', 'Male', 'Marr.', 'Pandahan', '1984-01-16', 42, '36-45', 'Moslem', '07R00017661', '0001134330489', '-', '-', 0, '6301031601840004', 'PANDAHAN RT 007 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70813', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Angkasa', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310014323896', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007042', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(4, '80007043', 'Surya Rahim', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2016-04-11', '10 y 03 m', 'Male', 'Marr.', 'Samarinda', '1985-12-15', 40, '36-45', 'Moslem', '16021325440', '0001924442908', '-', '-', 0, '6301051512850002', 'JL. A. YANI KM. 23,600 DESA PANDAHAN RT 002 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '2020', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Negeri 1 Martapura', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310011507202', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007043', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(5, '80007045', 'Mujairimi', 'WHS Spv', 1, 'Operator Skid Loader', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2013-06-03', '13 y 01 m', 'Male', 'Single', 'Liang Anggang', '1995-06-18', 31, '25-35', 'Moslem', '13007554093', '0001125235664', '-', '-', 0, '6301051806900001', 'LIANG ANGGANG RT 001 RW 001 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM UTILITY', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'C', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMP', 'Madrasah Tsanawiyah Darul Amanah', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310010075433', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80007045', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(6, '80007048', 'Supriyadi', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2013-06-03', '13 y 01 m', 'Male', 'Single', 'Blitar', '1973-05-09', 53, '46-55', 'Moslem', '11008857390', '0001140252096', '-', '-', 0, '6371030905730009', 'JL. KUIN SELATAN GG. H. ABDUSSAMAD RT 010 RW 001 KUIN CERUCUK - BANJARMASIN', 'Kalimantan Selatan', '70127', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMP', 'SMP PGRI Doko Blitar', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310001117376', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007048', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(7, '80007049', 'Roni Iswahyudi', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2013-06-03', '13 y 01 m', 'Male', 'Single', 'Kotabaru', '1980-03-05', 46, '46-55', 'Moslem', '13023240305', '0001152580678', '-', '-', 0, '6302060503800012', 'JL. HARASI RT 003 RW 002 - GAMBUT BANJAR', 'Kalimantan Selatan', '70652', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SD', '-', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310010150541', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007049', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(8, '80007052', 'Miswadi', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2013-06-03', '13 y 01 m', 'Male', 'Single', 'Banjarmasin', '1979-06-29', 47, '46-55', 'Moslem', '11029737712', '0001142303872', '-', '-', 0, '6371012907790010', 'JL. RANTAUAN DARAT GG. PEMBANGUNAN RT 010 RW 001 KELAYAN SELATAN - BANJARMASIN', 'Kalimantan Selatan', '70246', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 BANJARMASIN', 'Administrasi', 'Operative (NI)', 'Non Inc', '-', '0310010193988', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007052', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(9, '80007053', 'Fajar', 'Prod Shift Spv (Reg. B)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi B', '2014-02-19', '12 y 05 m', 'Male', 'Single', 'Pengayuan', '1993-09-09', 32, '25-35', 'Moslem', '14013348827', '0001547298944', '-', '-', 0, '6372040909930005', 'JL. A. YANI JURUSAN PELAIHARI KM. 26,200 RT 002 RW - LIANG ANGGANG LANDASAN ULIN LOKTABAT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMP', 'Madrasah Tsanawiyah Darul Amanah', 'Bahasa', 'Operative (NI)', 'Non Inc', '-', '0310014323961', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80007053', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(10, '80007057', 'Amberi', 'Prod Shift Spv (Reg. B)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi B', '2014-02-19', '12 y 05 m', 'Male', 'Single', 'Pandahan', '1977-05-25', 49, '46-55', 'Moslem', '14013348827', '0001452518289', '-', '-', 0, '6301052505770003', 'JL. TALOK BARU RT 006 RW 002 DS. PANDAHAN BATI-BATI BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT PAPER CUP B', '2020', 'NDL_02_02_12', 'PR_Paper Cup & Garpu Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT Tropis Service', '2834122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Ponpes Al Falah Putra', 'Bahasa', 'Operative (NI)', 'Non Inc', '-', '9000026166943', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80007057', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(11, '80007058', 'Hairani', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2013-06-03', '13 y 01 m', 'Male', 'Single', 'Pandahan', '1972-11-13', 53, '46-55', 'Moslem', '12042646542', '0001156528989', '-', '-', 0, '6301051311720002', 'PANDAHAN RT 001 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMP', 'Madrasah Tsanawiyah Negeri Gambut', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310010142415', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007058', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(12, '80007070', 'Muhamad Riduan', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2010-01-05', '16 y 06 m', 'Male', 'Single', 'Liang Anggang', '1990-04-02', 36, '36-45', 'Moslem', '10011101341', '0002659238695', '-', '-', 0, '6301050204900001', 'LIANG ANGGANG RT 004 RW 002 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000011252039', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007070', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(13, '80007073', 'Sugiannor', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2012-12-07', '13 y 07 m', 'Male', 'Single', 'Pandahan', '1992-01-13', 34, '25-35', 'Moslem', '13001827461', '0001124047146', '-', '-', 0, '6301051301920001', 'DS. PANDAHAN RT 001 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Angkasa', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310014324076', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80007073', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(14, '80008114', 'Riswandi', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2013-09-23', '12 y 10 m', 'Male', 'Single', 'Pandahan', '1995-09-07', 30, '25-35', 'Moslem', '13032013263', '0002442973375', '-', '-', 0, '6301050709950003', 'DS. PANDAHAN RT 008 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Angkasa', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000026123217', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008114', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(15, '80008116', 'Agus Riyadi', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2014-01-08', '12 y 06 m', 'Male', 'Single', 'Brebes', '1981-08-02', 44, '36-45', 'Moslem', '14003142057', '0001438096329', '-', '-', 0, '6301030208810008', 'DS. UJUNG BATU RT 007 RW - UJUNG BATU UJUNG BATU PELAIHARI', 'Kalimantan Selatan', '70851', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Angkasa', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000026167024', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008116', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(16, '80008135', 'Hendra Setiawan', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2014-08-11', '11 y 11 m', 'Male', 'Single', 'Banjarmasin', '1987-08-22', 38, '36-45', 'Moslem', '13001827420', '0001124047124', '-', '-', 0, '6371052108870005', 'JL. NUSA INDAH RT 005 RW 002 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA PGRI 2 Banjarmasin', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310010368093', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008135', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(17, '80008139', 'Abdul Solihin', 'Prod Shift Spv (Reg. C)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi C', '2014-06-04', '12 y 01 m', 'Male', 'Single', 'Banjarbaru', '1989-05-02', 37, '36-45', 'Moslem', '14019610329', '0002282039908', '-', '-', 0, '6372060205890001', 'JL. RAMBAI TIMUR NO.17 RT 002 RW 003 GUNTUNG PAIKAT BANJARBARU SELATAN BANJARBARU', 'Kalimantan Selatan', '70713', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Paket C', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000029611622', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80008139', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(18, '80008140', 'Rani', 'Prod Shift Spv (Reg. B)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi B', '2013-01-19', '13 y 06 m', 'Male', 'Single', 'Pandahan', '1992-04-17', 34, '25-35', 'Moslem', '13006772183', '0001125027821', '-', '-', 0, '6301051704920001', 'PANDAHAN RT 002 RW - PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Paket C', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000026167057', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80008140', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(19, '80008142', 'Rahman', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2010-10-28', '15 y 09 m', 'Male', 'Single', 'Kertak Hanyar', '1979-06-12', 47, '46-55', 'Moslem', '10029442471', '0001138620958', '-', '-', 0, '6371031206790006', 'JL. MAHLIGAI SIMPANG LIMAU RT 028 RW 002 SUNGAI LULUT BANJARMASIN TIMUR BANJARMASIN', 'Kalimantan Selatan', '70238', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'MI Negeri Kertak Hanyar', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310014373107', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008142', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(20, '80008153', 'Jumriani', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2012-11-07', '13 y 08 m', 'Male', 'Single', 'Sarang Halang', '1991-05-06', 35, '25-35', 'Moslem', '12039961680', '0001149181683', '-', '-', 0, '7401126110920005', 'JL. 45 RT 002 RW 001 SARANG HALANG PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Tunas Melati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310010944455', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008153', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(21, '80008171', 'Rahmani', 'GAS Spv', 1, 'Office Boy', 'Outsourcing', 'ADM HR', 'Non Produksi', '2010-09-04', '15 y 10 m', 'Male', 'Single', 'Pandahan', '1986-01-23', 40, '36-45', 'Moslem', '10029442539', '0001138625627', '-', '-', 0, '6301052501860003', 'JL. PASAR RT 009 RW 002 - BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000008509110', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80008171', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(22, '80019256', 'Suhaimi', 'Prod Shift Spv (Reg. C)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi C', '2020-07-03', '06 y 00 m', 'Male', 'Single', 'Pulau Alalak', '1991-07-03', 35, '25-35', 'Moslem', '20042448082', '0002941352998', '-', '-', 0, '6304050107910002', 'TAMBAN MUARA RT 001 RW 001 TAMBAN MUARA TAMBAN BARITO KUALA', 'Kalimantan Selatan', '70566', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallam', 'Agama', 'Operative (NI)', 'Non Inc', '-', '0310014271780', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80019256', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(23, '80027016', 'Muhammad Jerry Noval Ramadhan', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2021-06-10', '05 y 01 m', 'Male', 'Single', 'Tanah Laut', '2002-11-17', 23, '<25', 'Moslem', '18060577725', '0002370507096', '-', '-', 0, '6206030907950003', 'JL. A. YANI RT 001 RW 001 PANDAHAN BATI BATI BANJARBARU', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80027016', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(24, '80030438', 'Ardiya Martayuda', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2021-09-03', '04 y 10 m', 'Male', 'Single', 'Landasan Ulin', '2000-05-14', 26, '25-35', 'Moslem', '21062885971', '0003102392856', '-', '-', 0, '6372041405000003', 'JL. KURNIA RT 003 RW 003 LANDASAN ULIN UTARA LIANG ANGGANG BANJARBARU', 'Kalimantan Selatan', '70724', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 2 Banjarbaru', 'Teknik Kendaraan Ringan', 'Operative (NI)', 'Non Inc', '-', '0310015551362', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80030438', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(25, '80030440', 'Sudiyan', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2021-09-03', '04 y 10 m', 'Male', 'Single', 'Labolong', '1997-10-26', 28, '25-35', 'Moslem', '21062885963', '0001930588569', '-', '-', 0, '6301012610970002', 'JL. TAMBAK RT 021 RW 011 TABANIO TAKISUNG TANAH LAUT', 'Kalimantan Selatan', '70861', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Takisung', 'Teknik', 'Operative (NI)', 'Non Inc', '-', '031001555453', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80030440', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(26, '80030460', 'Muhammad Rasyid Sidik', 'Prod Shift Spv (Reg. A)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi A', '2021-11-26', '04 y 08 m', 'Male', 'Single', 'Tanah Laut', '2002-11-16', 23, '<25', 'Moslem', '21093708044', '0001887371471', '-', '-', 0, '6301051611020002', 'JL. SABILAL MUHTADIN JURUSAN UJUNG KAPUL RT 009 RW 006 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT PAPER CUP A', '1040', 'NDL_02_02_12', 'PR_Paper Cup & Garpu Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT Tropis Service', '2834122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80030460', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(27, '80042518', 'Ideham Khalid', 'WHS Spv', 1, 'Operator Skid Loader', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2022-11-03', '03 y 08 m', 'Male', 'Single', 'Hdl Birayang Atas', '1993-11-13', 32, '25-35', 'Moslem', '22130350741', '0002242276942', '-', '-', 0, '6301111311930001', 'DESA UJUNG RT 007 RW 002 UJUNG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM UTILITY', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'B', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Nurul Islam Kurau', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80042518', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(28, '80045704', 'Prastiyo Triwardoyo Wicaksono', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-04-01', '03 y 03 m', 'Male', 'Single', 'Martapura', '2000-10-24', 25, '25-35', 'Moslem', '23036252346', '0002658764531', '403075997732000', '-', 0, '6301052111000000', 'JL. KAMAJAYA NO. 10  RT 005 RW 002 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Tambang Ulang', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310016064266', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80045704', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(29, '80045719', 'Abdul Azis', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-04-01', '03 y 03 m', 'Male', 'Single', 'Tanah Laut', '2003-05-13', 23, '<25', 'Moslem', '23036252338', '0002659131033', '403294754732000', '-', 0, '6301051305030003', 'JL.TELOK SELONG \n RT 004 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Bati-Bati', 'Teknik Kendaraan Ringan Otomotif', 'Operative (NI)', 'Non Inc', '-', '0310017942711', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80045719', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(30, '80045720', 'Dicky Rahmatillah', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2023-04-01', '03 y 03 m', 'Male', 'Single', 'Tanah Laut', '2004-03-14', 22, '<25', 'Moslem', '23036252361', '0002206168986', '403013303732000', '-', 0, '6301051403040001', 'JL. A. YANI KOMPLEK LIANG ANGGANG PERMAI BLOK C/4  RT 005 RW 000 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310017942718', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80045720', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(31, '80045741', 'Muhammad Surya Badali', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2023-04-01', '03 y 03 m', 'Male', 'Single', 'Banjarbaru', '2002-10-18', 23, '<25', 'Moslem', '21098214055', '0001964011599', '534143318732000', '-', 0, '6303051810020005', 'JL. PENDIDIKANGANG PENGHULU  RT 005 RW 002 SEKUMPUL MARTAPURA BANJAR', 'Kalimantan Selatan', '70614', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Martapura', 'Multimedia', 'Operative (NI)', 'Non Inc', '-', '0310015920013', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80045741', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(32, '80045742', 'Muhammad Yusuf Pradana', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-04-01', '03 y 03 m', 'Male', 'Single', 'Tanah Laut', '2003-09-19', 22, '<25', 'Moslem', '21076182845', '0001202894752', '437280019732000', '-', 0, '6301051909030001', 'JL. 95 NO.16 \n RT 009 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310015595799', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80045742', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(33, '80046215', 'Muhammad Irwaji', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-04-10', '03 y 03 m', 'Male', 'Single', 'Tanah Laut', '2004-02-05', 22, '<25', 'Moslem', '23051185587', '0001191031615', '405625518732000', '-', 0, '6301050402040002', 'JL. A. YANI  KM 29 RT 007 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310018044795', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80046215', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(34, '80046216', 'M. Pahrurrazi', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-05-01', '03 y 02 m', 'Male', 'Single', 'Kapuas', '2003-04-19', 23, '<25', 'Moslem', '23051185579', '0000886569052', '406914564711000', '-', 0, '6203061904070001', 'SAKA LAGUN  RT 006 RW 000 SAKA LAGON PULAU PETAK KAPUAS', 'Kalimantan Tengah', '73592', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Muhammadiyah Kapuas', 'IPA', 'Operative (NI)', 'Non Inc', '-', '1590009716555', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80046216', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(35, '80046906', 'Muhammad Pahri', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2023-06-01', '03 y 01 m', 'Male', 'Single', 'Tanah Laut', '2004-01-11', 22, '<25', 'Moslem', '23074158678', '0002720925584', '393258942732000', '-', 0, '6301051101040003', 'JL. A. YANI RT 002 RW 002 BENUA RAYA BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '310018303696', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80046906', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(36, '80048148', 'Muhammad Baharudinnur', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-08-10', '02 y 11 m', 'Male', 'Single', 'Tanah Laut', '2004-07-30', 22, '<25', 'Moslem', '22089571644', '0001547168433', '604601211732000', '-', 0, '6301053007040001', 'GANG 95  RT 001 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310016653241', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80048148', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(37, '80048518', 'Riad Noor', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2023-09-01', '02 y 10 m', 'Male', 'Single', 'Banjarmasin', '2000-06-08', 26, '25-35', 'Moslem', '-', '0002721887065', '-', '-', 0, '6301050806000001', 'KOMP. MULTIMADYA RT 09 RW 05 DESA LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP A', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'A', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Gambut', 'Teknik Otomotif', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80048518', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(38, '80048798', 'Muhammad Reski', 'GAS Spv', 1, 'Driver', 'Outsourcing', 'ADM HR', 'Non Produksi', '2023-09-01', '02 y 10 m', 'Male', 'Single', 'Pantai Hambawang', '1993-08-06', 32, '25-35', 'Moslem', '23129761922', '0002767551952', '-', '-', 0, '6301050608930001', 'KOMP MULTI MADYA RT 010 RW 005 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', '-', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80048798', '8 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(39, '80049036', 'Daisy Maya Puspitasari', 'GAS Spv', 1, 'Receptionist', 'Outsourcing', 'ADM HR', 'Non Produksi', '2023-10-01', '02 y 09 m', 'Female', 'Single', 'Jakarta', '1994-01-07', 32, '25-35', 'Moslem', '23080802665', '0001849396419', '392467270732000', '-', 0, '3376024701940002', 'JALAN ABADI III KOMP. GRAND PALAM MEGATAMA NO. 65 RT 006 RW 007 GUNTUNG MANGGIS LANDASAN ULIN BANJARBARU', 'Kalimantan Selatan', '70721', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'S1', 'Universitas Teknologi Yogyakarta', 'Satra Inggris', 'Operative (NI)', 'Non Inc', '-', '0310018254253', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80049036', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(40, '80049496', 'Ahmad Fiqri Almadani', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2023-11-01', '02 y 08 m', 'Male', 'Single', 'Tanah Laut', '2001-06-16', 25, '25-35', 'Moslem', '23160315521', '0000889666378', '508339579732000', '-', 0, '6301051606010001', 'JL. TELUK MARJAYA  RT 006 RW 002 UJUNG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '310018914351', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80049496', '7 Jam', '2026-10-05 17:24:02', '2026-10-05 23:20:48'),
(41, '80049635', 'Muhammad Noorafriansyah', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2023-11-01', '02 y 08 m', 'Male', 'Single', 'Samarinda', '1993-04-17', 33, '25-35', 'Moslem', '-', '0000977885548', '-', '-', 0, '6301051704930001', 'DESA BANYU IRANG RT 009 RW 002 BANYU IRANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP C', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'C', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80049635', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(42, '80050580', 'Muhammad Rizky Yudha Pratama', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Bentok', '1997-12-08', 28, '25-35', 'Moslem', '16028802987', '0001770275981', '941075541732000', '-', 0, '6301050805970001', 'JL. A. YANI RT 005 RW 002 BENTOK KAMPUNG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310015047437', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80050580', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(43, '80050581', 'Muhammad Sidik', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Tanah Laut', '2002-06-20', 24, '<25', 'Moslem', '21050399324', '0001547168253', '437782253732000', '-', 0, '6301052006020001', 'GANG SABRINA NO. 45  RT 007 RW 002 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'MIPA', 'Operative (NI)', 'Non Inc', '-', '0310015612131', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80050581', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(44, '80050582', 'Muhammad Rasyid', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Pandahan', '1997-12-18', 28, '25-35', 'Moslem', '22048491330', '0002658774058', '652969767732000', '-', 0, '6301051810970001', 'JL. A. YANI KM 28,2 \n RT 001 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Al Falah Banjarbaru', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310016315791', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80050582', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(45, '80050583', 'Fiqri Aditya Al-Fahmi', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Landasan Ulin', '1995-03-25', 31, '25-35', 'Moslem', '14043728634', '0001634891725', '734035454732000', '-', 0, '6372022503950003', 'JL. KAMPUNG BARU NO. 13 RT 002 RW 002 LANDASAN ULIN TIMUR LANDASAN ULIN BANJARBARU', 'Kalimantan Selatan', '70724', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Al Falah Banjarbaru', 'IPS', 'Operative (NI)', 'Non Inc', '-', '310018075658', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80050583', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(46, '80050584', 'Aulia Rahman', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Tanah Laut', '2005-07-12', 21, '<25', 'Moslem', '24001159177', '0001546249083', '996484325732000', '-', 0, '6301051207050002', 'JL. MELATI  RT 010 RW 003 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '310019204158', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80050584', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(47, '80050606', 'Dzaki Fuad Hafidzi', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-01-02', '02 y 06 m', 'Male', 'Single', 'Banjarmasin', '2004-04-23', 22, '<25', 'Moslem', '-', '0001546249566', '-', '-', 0, '6301032304040002', 'JL. H.M JAFRI BALIREJO 188 B RT 018 RW 006 ANGSAU PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP A', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'A', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Gambut', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80050606', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(48, '80051460', 'Nur Aulia', 'Prod Shift Spv (Reg. A)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-02-01', '02 y 05 m', 'Female', 'Single', 'Tanah Laut', '2004-05-07', 22, '<25', 'Moslem', '24032901936', '0002720940197', '-', '-', 0, '6301054705040004', 'KOMPLEK PESONA PONDOK INDAH  RT 012 RW 006 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80051460', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(49, '80051462', 'Riyana', 'Prod Shift Spv (Reg. B)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-02-01', '02 y 05 m', 'Female', 'Single', 'Tanah Laut', '2002-03-02', 24, '<25', 'Moslem', '24032901951', '0002720706309', '655347490732000', '-', 0, '6301054203020001', 'JL. KAMAJAYA NO. 09  RT 003 RW 001 UJUNG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '310019307217', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80051462', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(50, '80051463', 'Nafisatul Hayati', 'Prod Shift Spv (Reg. A)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-02-01', '02 y 05 m', 'Female', 'Single', 'Tanah Laut', '2004-05-14', 22, '<25', 'Moslem', '24032901969', '0001199920048', '-', '-', 0, '6301055405040001', 'KOMPLEK GRIYA PERMATA II RT  RW  LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310019307274', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80051463', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(51, '80051464', 'Syendi Putra Rabbani', 'Prod Shift Spv (Reg. C)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-02-01', '02 y 05 m', 'Male', 'Single', 'Landasan Ulin', '2005-04-10', 21, '<25', 'Moslem', '24058979394', '0003225715042', '-', '-', 0, '6372041004050002', 'KOMPLEK WENGGA JAYA IV BLOK B-45  RT 006 RW 004 LANDASAN ULIN UTARA LIANG ANGGANG BANJARBARU', 'Kalimantan Selatan', '70723', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 2 Banjarbaru', 'Teknik Pembangkit Tenaga Listrik', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80051464', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(52, '80051465', 'Ahmad Rifa\'i', 'Prod Shift Spv (Reg. A)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-02-01', '02 y 05 m', 'Male', 'Single', 'Pandahan', '1995-04-25', 31, '25-35', 'Moslem', '24058979386', '0002659131494', '-', '-', 0, '6301052504950002', 'JL.TELOK SELONG  RT 004 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Paket C - Gajah Mada', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80051465', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(53, '80051466', 'Muhammad Rezky', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-02-01', '02 y 05 m', 'Male', 'Single', 'Tanah Laut', '2003-12-27', 22, '<25', 'Moslem', '22125939896', '0003299720174', '-', '-', 0, '6301052710030002', 'JL. PERINTIS RAYA RT 005 RW 003 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310017291068', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80051466', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(54, '80052023', 'Dani', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-03-04', '02 y 04 m', 'Male', 'Single', 'Tanah Laut', '2006-02-10', 20, '<25', 'Moslem', '-', '0002658953744', '-', '-', 0, '6301061002060003', 'JL. MUSTOFA IDE HAM RT 003 RW 000 PANYIPATAN TANAH LAUT TANAH LAUT', 'Kalimantan Selatan', '70871', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP D', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'D', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA TANAH LAUT', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '5. Shift - Regu D', '80052023', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(55, '80052261', 'Muhammad Ridho', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-03-04', '02 y 04 m', 'Male', 'Single', 'Pandahan', '1999-04-20', 27, '25-35', 'Moslem', '24058979352', '0002721946768', '125790725732000', '-', 0, '6301052004980001', 'JL.TELOK SELONG  RT 005 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP B', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'PKBM Banua', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310019455594', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80052261', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(56, '80052262', 'Aydina Fitri', 'Prod Shift Spv (Reg. C)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-03-04', '02 y 04 m', 'Female', 'Single', 'Pandahan', '2002-12-07', 23, '<25', 'Moslem', '24058979360', '0000890071986', '076027945732000', '-', 0, '6301054712020002', 'JL. PELABUHAN RT 001 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 4 Banjarbaru', 'Ipa', 'Operative (NI)', 'Non Inc', '-', '0310018804529', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80052262', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(57, '80052272', 'Tedi Hermawan', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-03-04', '02 y 04 m', 'Male', 'Single', 'Tanah Laut', '2001-07-17', 25, '25-35', 'Moslem', '24070608245', '0002250834873', '-', '-', 0, '6301051707010001', 'JL. LAMBENTENG  RT 006 RW 002 UJUNG BARU BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', ' Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310019462822', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80052272', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(58, '80053247', 'Muhamad Alwi', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-03-17', '02 y 04 m', 'Male', 'Single', 'Banjarmasin', '2000-11-10', 25, '25-35', 'Moslem', '-', '0002942573962', '-', '-', 0, '6304011011000005', 'DESA TABUNGANEN KECIL RT 001 RW 000 TABUNGANEN KECIL TABUNGANEN BARITO KUALA', 'Kalimantan Selatan', '70582', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP C', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'C', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA BANJARMASIN', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80053247', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48');
INSERT INTO `employees` (`id`, `nik`, `nama`, `user`, `jumlah`, `jabatan`, `employee_group`, `pers_subarea_text`, `sub_subarea`, `tmk`, `length_of_service`, `gender`, `marital_status`, `birthplace`, `birthdate`, `age`, `range_umur`, `religion`, `jamsostek_id`, `bpjs_id`, `tax_id`, `tax_status`, `number_of_child`, `ktp_nomor`, `ktp_alamat`, `region`, `postal_code`, `no_hp`, `email`, `esgrp_text`, `level`, `position_id`, `org_unit_id`, `org_unit_text`, `pers_subarea_id`, `org_key_id`, `org_key_text`, `bantu_opr_hlp`, `regu_line`, `line`, `regu`, `area_prov_marketing`, `distrik_rayon_marketing`, `vendor_os`, `cost_center`, `cost_center_text`, `emp_status_text`, `excuse`, `tanggal_akhir_kerja`, `pendidikan_level`, `pendidikan_institusi`, `pendidikan_studi`, `ps_type_text`, `status_incentive`, `bank_keys`, `bank_account_no`, `vaksin1_tanggal`, `vaksin1_merk`, `vaksin2_tanggal`, `vaksin2_merk`, `vaksin3_tanggal`, `vaksin3_merk`, `workschedule_id`, `tipe_hari_kerja`, `shift_type`, `no_sidik_jari`, `jam_kerja`, `created_at`, `updated_at`) VALUES
(59, '80053355', 'Muhammad Rafli', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-03-25', '02 y 04 m', 'Male', 'Single', 'Tanah Laut', '2005-11-07', 20, '<25', 'Moslem', '24070608252', '0002720942458', '-', '-', 0, '6301050711050001', 'JL. INDUSTRI RT 009 RW 002 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Bati-Bati', 'Teknik Otomotif', 'Operative (NI)', 'Non Inc', '-', '0310019463192', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80053355', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(60, '80053356', 'Nuril Aulia Ningrum', 'Prod Shift Spv (Reg. B)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-04-16', '02 y 03 m', 'Female', 'Single', 'Tanah Laut', '2001-08-20', 24, '<25', 'Moslem', '24070608278', '0002930758479', '-', '-', 0, '6301056008010001', 'JL. A. YANI RT 001 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Agribisnis Tanaman Perkebunan', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80053356', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(61, '80053357', 'Mahmud Arifin Ilham', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-04-15', '02 y 03 m', 'Male', 'Single', 'Tanah Laut', '2005-08-07', 20, '<25', 'Moslem', '24070608260', '0002721947962', '-', '-', 0, '6301050708050002', 'KOMPLEK LIANG ANGGANG PERMAI RT 002 RW 001 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80053357', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(62, '80054227', 'Indra Adriyani', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-01', '02 y 02 m', 'Male', 'Single', 'Banjarmasin', '2005-06-22', 21, '<25', 'Moslem', '-', '0002741368937', '-', '-', 0, '6371052206050004', 'JL. SIMPANG SUNGAI MESA NO. 50  RT 012 RW 002 SEBERANG MESJID BANJARMASIN TENGAH BANJARMASIN', 'Kalimantan Selatan', '70231', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP A', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri PGRI 1 BANJARMASIN', 'MIPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054227', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(63, '80054228', 'Muhammad Hidayatullah', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-01', '02 y 02 m', 'Male', 'Single', 'Tanah Laut', '2002-07-11', 24, '<25', 'Moslem', '21089990739', '0002721820599', '139573208732000', '-', 0, '6301051107020001', 'JL. HIJRAH  RT 002 RW 001 BATI-BATI BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS PAPER CUP', '2030', 'NDL_02_03_08', 'WH_Paper Cup & Garpu', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2834126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054228', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(64, '80054261', 'Rani Feriansyah', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-06', '02 y 02 m', 'Male', 'Single', 'Balikpapan', '1988-02-02', 38, '36-45', 'Moslem', '-', '0001156652908', '-', '-', 0, '6371030202880008', 'JALAN TIGA RODA  RT 008 RW 000 SARI GADUNG SIMPANG EMPAT TANAH BUMBU', 'Kalimantan Selatan', '72211', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'A', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri PGRI 2 Banjarmasin', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054261', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(65, '80054263', 'Muhammad Rifqi Hidayat', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Barabai', '2004-12-12', 21, '<25', 'Moslem', '-', '0002230331938', '-', '-', 0, '6307061212040003', 'KAYU BAWANG  RT 008 RW 002 KAYU BAWANG BARABAI HULU SUNGAI TENGAH', 'Kalimantan Selatan', '71315', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP C', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'C', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'PKBM Kayu Bawang', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054263', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(66, '80054264', 'Alifinozva Gigih Mustika', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Tanah Laut', '2004-08-03', 21, '<25', 'Moslem', '-', '0002431853987', '-', '-', 0, '6372020308040008', 'JL. KARANGREJO KOMP. GRAHA CITRA INDAH PERMAI RT 050 RW 001 GUNTUNG MANGGIS GUNTUNG MANGGIS BANJARBARU', 'Kalimantan Selatan', '70724', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP B', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'B', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA BANJARBARU', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054264', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(67, '80054265', 'Arris Surahmadi Ahmad', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Banjarbaru', '1994-06-11', 32, '25-35', 'Moslem', '-', '0001379424802', '-', '-', 0, '6301031106940003', 'JL. ANTESA RT 5B RT 005 RW 002 PELAIHARI PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70812', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP B', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'B', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Pelaihari', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054265', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(68, '80054266', 'Bagus Kurniawan', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Banjarmasin', '1987-09-22', 38, '36-45', 'Moslem', '-', '0001609746333', '-', '-', 0, '6371052209870008', 'JL. A. YANI KM 32 RT 012 RW 000 LIANG ANGGANG BATI BATI ANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP B', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'B', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Teknik Otomotif', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054266', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(69, '80054267', 'Bowo Kurnianto', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Jakarta', '1989-01-11', 37, '36-45', 'Moslem', '-', '0001609746208', '-', '-', 0, '6301051101890001', 'JL. SAKULA I RT 012 RW 004 BATI-BATI BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP C', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'C', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054267', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(70, '80054269', 'Mulyadi', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Munggu', '1985-09-25', 40, '36-45', 'Moslem', '-', '0001816383633', '-', '-', 0, '6303122509850001', 'SUNGAI BOKOR RT 004 RW 002 PEMATANG DANAU MATARAMAN BANJAR', 'Kalimantan Selatan', '70671', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP A', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'A', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Mataraman', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054269', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(71, '80054270', 'Ardiansyah', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-05-07', '02 y 02 m', 'Male', 'Single', 'Handil Birayang Atas', '1990-05-01', 36, '36-45', 'Moslem', '-', '0001609747503', '-', '-', 0, '6301050105900003', 'JL. A YANI RT.004/ RW 002 KEC. LIANG ANGGANG KEC BATI-BATI KAB TANAH LAUT KALIMANTAN SELATAN RT 004 RW 002 DESA LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP D', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'D', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Ilmu Pengetahuan Alam', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '5. Shift - Regu D', '80054270', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(72, '80054647', 'Muhammad Zainal Arifin', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Tanah Laut', '2005-12-03', 20, '<25', 'Moslem', '-', '0000888812008', '203899836732000', '-', 0, '6301050312050002', 'JL. MASJID IHYAUDDIN RT 007 RW 003 BENUA RAYA BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM POWDER,OIL & SEASONING', '2030', 'NDL_02_03_02', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim Ulya', ' Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310019724171', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054647', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(73, '80054648', 'Muhammad Andre', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Banjarmasin', '2003-03-29', 23, '<25', 'Moslem', '-', '0001114260309', '652539180731000', '-', 0, '6301052903030003', 'JL. SUKA MAJU\n RT 005 RW 002 UJUNG BARU BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP A', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310019740342', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054648', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(74, '80054649', 'Asyrafi Al Khair', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Tanah Laut', '2001-08-23', 24, '<25', 'Moslem', '-', '0002626261244', '204687966732000', '-', 0, '6301052308010002', 'KOMP.PESONA PONDOK INDAH\n RT 012 RW 006 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310019732216', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054649', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(75, '80054650', 'Muhammad Rifani', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Padang', '2000-11-29', 25, '25-35', 'Moslem', '-', '0001885903255', '203577291732000', '-', 0, '6301052911000001', 'JL. INDUSTRI RT 009 RW 002 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Pondok Pesantren Darussalim Bati-Bati', ' Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310019732950', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054650', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:48'),
(76, '80054652', 'Akhmad Zidan Rifa\'I', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Tanah Laut', '2000-05-16', 26, '25-35', 'Moslem', '-', '0003056711725', '208271973732000', '-', 0, '6301051605000002', 'JL. A. YANI KOMPLEK MULTI MADYA NO.32\n RT 010 RW 005 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP B', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'D3', 'POLITEKNIK HASNUR', 'Teknik Informatika', 'Operative (NI)', 'Non Inc', '-', '0310019729998', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054652', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(77, '80054653', 'Muhammad Hilman Firdaus', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-05-27', '02 y 02 m', 'Male', 'Single', 'Martapura', '2005-06-16', 21, '<25', 'Moslem', '-', '0002873167187', '204074165732000', '-', 0, '6303051506050002', 'JL. DARUSSALAM GANG AMAN RT 011 RW 003 TANJUNG REMA TANJUNG REMA BANJAR', 'Kalimantan Selatan', '70612', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP B', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMAN 1 MARTAPURA', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310019742884', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054653', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(78, '80054706', 'Andrie Roesdianto', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Gambut', '1989-07-08', 37, '36-45', 'Moslem', '07R00136891', '0001277848271', '981636889732000', '-', 0, '6372022505800009', 'JALAN KASTURI II GANG PEMBATAAN RT 027 RW 006 SYAMSUDDINNOOR LANDASAN ULIN BANJAR BARU', 'Kalimantan Selatan', '70652', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP C', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Gambut', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054706', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(79, '80054707', 'Rahmadani', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Kayu Bawang', '1988-07-25', 38, '36-45', 'Moslem', '12001283832', '0001546249329', '981478407732000', '-', 0, '6303032507880002', 'JALAN IRIGASI RT 004 RW 003 KAYU BAWANG GAMBUT BANJAR', 'Kalimantan Selatan', '70652', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Gambut', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054707', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(80, '80054711', 'Fahmi Yusuf', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Padang', '1994-10-23', 31, '25-35', 'Moslem', '13005213221', '0001546395941', '982661811732000', '-', 0, '6301052310940001', 'PERUM GRIYA ADELLIN UTAMA BLOK B NO.17  RT 004 RW 002 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054711', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(81, '80054712', 'Kholilurrahman', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Bangkal', '1987-09-30', 38, '36-45', 'Moslem', '14002994664', '0001804144421', '668986201732000', '-', 0, '6301053009870004', 'JALAN MELATI NO 6 RT 007 RW 003 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP C', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallam', 'Paket C Agama', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054712', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(82, '80054713', 'Noor Ipansyah                ', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Handil Birayang Atas', '1989-08-03', 36, '36-45', 'Moslem', '14008721723', '0001546396255', '731674180732000', '-', 0, '6301050803890005', 'LIANG ANGGANG RT 004 RW 002 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Gambut', 'Teknik Otomotif', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054713', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(83, '80054714', 'Ferry Fadely', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Banjarmasin', '1991-11-20', 34, '25-35', 'Moslem', '12008026325', '0001546396108', '731673794732000', '-', 0, '6301062011910003', 'GANG SABRINA KOMP LANGGENG CITRA PERSADA BLOK A NO 16 RT 007 RW 002 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK 2 Desember Pelaihari', 'Listrik', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054714', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(84, '80054715', 'Rusdiansyah', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Kayu Bawang', '1998-01-08', 28, '25-35', 'Moslem', '20062838451', '0003051277975', '953469665732000', '-', 0, '6303030108960002', 'JALAN HANDIL ALABIO RT 002 RW 000 KAYU BAWANG GAMBUT BANJAR', 'Kalimantan Selatan', '70652', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Gambut', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054715', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(85, '80054716', 'Rifani', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Pangkuh', '1993-07-08', 33, '25-35', 'Moslem', '13004769074', '0001609746816', '981478506732000', '-', 0, '6303130708930004', 'TAMBAK SARINAH RT 002 RW 001 TAMBAK SARINAH KURAU TANAH LAUT', 'Kalimantan Selatan', '70853', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM POWDER,OIL & SEASONING', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA 1 Beruntung Baru', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054716', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(86, '80054717', 'Fahmi Amrullah', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Jatuh', '1989-11-19', 36, '36-45', 'Moslem', '13005213213', '0001609747143', '983496894732000', '-', 0, '6307051911890001', 'GANG SABRINA KOMPLEK LANGGENG JAYA BLOK E NO 131 RT 007 RW 002 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Barabai', 'Bisnis &Manajemen', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054717', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(87, '80054718', 'Muhammad Habibi', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Pandahan', '1994-08-20', 31, '25-35', 'Moslem', '13031168449', '0001546395928', '985710235732000', '-', 0, '6301052008940002', 'JALAN A. YANI KM.29.100 RT 007 RW 002 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054718', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(88, '80054721', 'Yandi Kurniawan', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Pelaihari', '1989-02-07', 37, '36-45', 'Moslem', '63010302078', '0000122379355', '759015571732000', '-', 0, '6301030207890004', 'JALAN BHAKTI RT 003 RW 001 KARANG TARUNA PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70812', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri  1 Pelaihari', 'Adm. Perkantoran', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054721', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(89, '80054722', 'Muhammad Kevin Setiadi', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Banjarmasin', '1996-10-06', 29, '25-35', 'Moslem', '19089599823', '0002905406302', '933065203736000', '-', 0, '6371050610960010', 'JALAN SIMPANG SUNGAI MESA NO. 50 RT 012 RW 002 SEBERANG MESJID BANJARMASIN TENGAH BANJARMASIN', 'Kalimantan Selatan', '70233', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 2 Banjarmasin', 'Pekerjaan Sosial', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054722', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(90, '80054724', 'Rohani', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Tanah Laut', '1993-05-03', 33, '25-35', 'Moslem', '14034631243', '0001894234274', '735684383732000', '-', 0, '6301044503930001', 'JALAN A.YANI RT 002 RW 001 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Kurau', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054724', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(91, '80054725', 'Muhammad Aldi', 'Prod Shift Spv (Reg. A)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Tanah Laut', '2003-11-24', 22, '<25', 'Moslem', '20361354273', '0000889675593', '206751182732000', '-', 0, '6301112411030001', 'DESA HANDIL BIRAYANG BAWAH RT 001 RW 000 HANDIL BIRAYANG BAWAH BUMI MAKMUR TANAH LAUT', 'Kalimantan Selatan', '70853', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri Bumi Makmur', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80054725', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(92, '80054726', 'Muhammad Ferdi', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Loktangga', '2005-07-19', 21, '<25', 'Moslem', '-', '0003577465247', '203613542732000', '-', 0, '6303061901050001', 'DESA SUNGAI BESAR RT 002 RW 000 SUNGAI BESAR KARANG INTAN BANJAR', 'Kalimantan Selatan', '70661', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Darusallam MA', 'Teknik Bisnis Sepeda Motor', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054726', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(93, '80054727', 'Muhammad Ramadhan', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-09-01', '01 y 10 m', 'Male', 'Single', 'Tanah Laut', '2005-08-10', 20, '<25', 'Moslem', '-', '0002721895931', '-', '-', 0, '6301051010060002', 'JALAN SUKA MAJU RT 005 RW 002 UJUNG BARU BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80054727', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(94, '80054728', 'Sania Lutfiansyah', 'Prod Shift Spv (Reg. C)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-06-01', '02 y 01 m', 'Female', 'Single', 'Lamongan', '2005-05-07', 21, '<25', 'Moslem', '-', '0002721925203', '-', '-', 0, '6301054507050004', 'JALAN NUSA INDAH  RT 008 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri Pertanian Pembangunan Pelaihari', 'Agribisnis Ternak', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80054728', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(95, '80054729', 'Muhammad Zaini', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Tanah Laut', '2002-07-01', 24, '<25', 'Moslem', '23170016218', '0002659238831', '206839474732000', '-', 0, '6301050608060002', 'JALAN MELATI RT 010 RW 004 BATI-BATI BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80054729', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(96, '80055212', 'Abdul Hadi', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Gambut', '1989-08-07', 36, '36-45', 'Moslem', '-', '0001546275868', '-', '-', 0, '6303030708890006', 'JALAN A. YANI KM 28 RT 001 RW 001 PANDAHAN BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'B', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Gambut', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80055212', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(97, '80055213', 'Paitu Rahman', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Gambut', '1993-11-12', 32, '25-35', 'Moslem', '-', '0001546275328', '-', '-', 0, '6301051211930003', 'JALAN PESANTREN RT 002 RW 001 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS PAPER CUP', '2030', 'NDL_02_03_08', 'WH_Paper Cup & Garpu', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2834126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80055213', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(98, '80055214', 'Masnawi', 'WHS Spv', 1, 'Operator Skid Loader', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-06-01', '02 y 01 m', 'Male', 'Single', 'Lok Gabang', '1982-02-14', 44, '36-45', 'Moslem', '-', '0001609747086', '-', '-', 0, '6303071402820003', 'DESA PULAU SARI RT 002 RW 001 PULAU SARI TAMBANG ULANG TANAH LAUT', 'Kalimantan Selatan', '70854', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM UTILITY', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'A', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Negeri 4 Martapura', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80055214', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(99, '80055510', 'Kastalani', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-07-01', '02 y 00 m', 'Male', 'Single', 'Sungai Kali', '1997-10-10', 28, '25-35', 'Moslem', '-', '0002057817813', '197949514731000', '-', 0, '6304141010970001', 'SUNGAI KALI RT 004 RW 002 SUNGAI KALI BARAMBAI BARITO KUALA', 'Kalimantan Selatan', '70562', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP A', '2030', 'NDL_02_03_03', 'WH_Finish Goods', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Wanaraya', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310019862112', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80055510', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(100, '80055800', 'Ellien Marlina', 'IR Spv', 1, 'Paramedic', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-01', '02 y 00 m', 'Female', 'Single', 'Pelaihari', '1986-03-21', 40, '36-45', 'Moslem', '-', '0001100151009', '156969586732000', '-', 0, '6301056103860004', 'JL. KOMP MULTI MADYA  RT 009 RW 005 LIANG ANGGANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'IR & PR', '1040', 'NDL_01_03_04', 'HR_Industrial Relation', '-', '-', '-', 'B', '-', '-', 'CV. Wafaiza Bati-Bati', '2813330000', 'Personnel - Admint', 'Active', '-', NULL, 'S1', 'Sekolah Tinggi Ilmu Kesehatan Muhammadiyah Banjarmasin', 'Perawat', 'Operative (NI)', 'Non Inc', '-', '0310006390028', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80055800', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(101, '80055801', 'Didi Suryadi', 'IR Spv', 1, 'Paramedic', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-01', '02 y 00 m', 'Male', 'Single', 'Kurau', '1989-07-08', 37, '36-45', 'Moslem', '-', '0002067408167', '-', '-', 0, '6301040807890006', 'JL. SIAM RT 009 RW 003 KURAU KURAU TANAH LAUT', 'Kalimantan Selatan', '70853', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'IR & PR', '1040', 'NDL_01_03_04', 'HR_Industrial Relation', '-', '-', '-', 'A', '-', '-', 'CV. Wafaiza Bati-Bati', '2813330000', 'Personnel - Admint', 'Active', '-', NULL, 'D3', 'Akademi Keperawatan Pandan Harum Banjarmasin', 'Keperawatan', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80055801', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(102, '80055802', 'Endah Budiati', 'IR Spv', 1, 'Dokter', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-01', '02 y 00 m', 'Female', 'Single', 'Banjarbaru', '1973-11-25', 52, '46-55', 'Moslem', '-', '0000172822511', '-', '-', 0, '6372056511730001', 'JL. TRIPOLI W 06 KOMP BALITRA JAYA PERMAI RT 001 RW 008 LOKTABAT UTARA BANJARBARU UTARA BANJARBARU', 'Kalimantan Selatan', '70714', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'IR & PR', '1040', 'NDL_01_03_04', 'HR_Industrial Relation', '-', '-', '-', '-', '-', '-', 'CV. Wafaiza Bati-Bati', '2813330000', 'Personnel - Admint', 'Active', '-', NULL, 'S1', 'Universitas Lambung Mangkurat', 'Kedokteran', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80055802', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(103, '80055803', 'Siti Yunita Andhini', 'IR Spv', 1, 'Bidan', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-01', '02 y 00 m', 'Female', 'Single', 'Pelaihari', '1975-06-28', 51, '46-55', 'Moslem', '-', '0000122307546', '-', '-', 0, '6301056806750001', 'GANG SABRINA NUSA INDAH JL. MUFAKAT III RT 007 RW 002 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'IR & PR', '1040', 'NDL_01_03_04', 'HR_Industrial Relation', '-', '-', '-', '-', '-', '-', 'CV. Wafaiza Bati-Bati', '2813330000', 'Personnel - Admint', 'Active', '-', NULL, 'D3', '-', '-', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80055803', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(104, '80056325', 'Farid Wajidi', 'IR Spv', 1, 'Paramedic', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-08-01', '01 y 11 m', 'Male', 'Single', 'Pelaihari', '1991-03-26', 35, '25-35', 'Moslem', '-', '0000122307546', '-', '-', 0, '6301056806750001', 'JL. A. YANI RT 002 RW 002 BATI-BATI BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'IR & PR', '1040', 'NDL_01_03_04', 'HR_Industrial Relation', '-', '-', '-', 'C', '-', '-', 'CV. Wafaiza Bati-Bati', '2813330000', 'Personnel - Admint', 'Active', '-', NULL, 'D3', 'Akademi Keperawatan Intan Martapura', 'Keperawatan', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80056325', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(105, '80056343', 'M. Zainal A', 'GAS Spv', 1, 'Driver', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-24', '02 y 00 m', 'Male', 'Single', 'Tanah Laut', '2003-12-22', 22, '<25', 'Moslem', '-', '0002721800698', '-', '-', 0, '6301052212030001', 'NUSA INDAH  RT 003 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Bati-Bati', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80056343', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(106, '80056344', 'Muhammad Alfiranoor Akbar', 'GAS Spv', 1, 'Driver', 'Outsourcing', 'ADM HR', 'Non Produksi', '2024-07-26', '02 y 00 m', 'Male', 'Single', 'Banjarbaru', '2000-09-19', 25, '25-35', 'Moslem', '-', '0003305901633', '-', '-', 0, '6303051909000004', 'KOMPLEK TAMAN CITRA KERATON RT 011 RW 000 SUNGAI SIPAI MARTAPURA BANJAR', 'Kalimantan Selatan', '70612', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK YPK Banjarbaru', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80056344', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(107, '80057001', 'Muhammad Hadani', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-09-01', '01 y 10 m', 'Male', 'Single', 'Tanah Laut', '2006-08-06', 19, '<25', 'Moslem', '-', '0002943679015', '-', '-', 0, '6301050608060002', 'JALAN A. YANI NO. 29  RT 002 RW 002 BENUA RAYA BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_01', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'Ilmu-ilmu Keagamaan Islam', 'Operative (NI)', 'Non Inc', '-', '0310019725392', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80057001', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(108, '80057002', 'Rika Damayanti', 'Prod Shift Spv (Reg. A)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi A', '2024-09-01', '01 y 10 m', 'Female', 'Single', 'Tanah Laut', '2005-05-23', 21, '<25', 'Moslem', '-', '0002437203025', '127331817732000', '-', 0, '6301056303050001', 'JALAN 95 RT 009 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_01', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '9000016688054', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80057002', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(109, '80057003', 'Hasbi Ridoni', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-09-09', '01 y 10 m', 'Male', 'Single', 'Ujung Baru', '2003-10-20', 22, '<25', 'Moslem', '-', '0002721926935', '-', '-', 0, '6301052010030004', 'JL. A.YANI  \n RT 004 RW 002 UJUNG BARU BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_01', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310020173160', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80057003', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(110, '80057004', 'M. Zaenal Muttaqin', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2024-09-09', '01 y 10 m', 'Male', 'Single', 'Padang', '2006-05-04', 20, '<25', 'Moslem', '-', '0002943681502', '-', '-', 0, '6301050405060002', 'JL. INDUSTRI RT 008 RW 003 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_01', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Darussalim Bati-Bati', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80057004', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(111, '80057278', 'Heri Anwar Gamal', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2024-09-01', '01 y 10 m', 'Male', 'Single', 'Tanah Laut', '2003-04-07', 23, '<25', 'Moslem', '-', '0000889965955', '-', '-', 0, '6301030704030001', 'DS, PANJARATAN RT 002 RW 001 PANJARATAN PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70815', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Takisung', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80057278', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(112, '80058986', 'Muhammad Rehan', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2024-12-01', '01 y 07 m', 'Male', 'Single', 'Tanah Laut', '2003-08-28', 22, '<25', 'Moslem', '-', '0002721963903', '-', '-', 0, '6301052808030001', 'JL. INDUSTRI RT 008 RW 003 PADANG BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310019978538', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80052273', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(113, '80059654', 'Muhammad Abdi Alpiannor', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Tanah Laut', '2002-09-10', 23, '<25', 'Moslem', '-', '0000888857199', '-', '-', 0, '6301051009020005', 'JL. KAMAJAYA RT 005 RW 002 PADANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP C', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '310020146281', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80059654', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(114, '80059655', 'Ahmad Rudini', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Tanah Laut', '2000-09-29', 25, '25-35', 'Moslem', '-', '0001885950617', '286236815732000', '-', 0, '6301112909000001', 'DESA HANDIL BIRAYANG NO. 26D RT 005 RW 002 HANDIL BIRAYANG BAWAH BUMI MAKMUR TANAH LAUT', 'Kalimantan Selatan', '70853', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP C', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Nurul Iman Kurau', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310020903301', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80059655', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(115, '80059656', 'Muhammad Riski Yannor', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Bati-Bati', '2002-09-07', 23, '<25', 'Moslem', '-', '0001928954687', '055224570732000', '-', 0, '6301050709020003', 'JL. KELUARGA RT 006 RW 001 RT 006 RW 001 PADANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310020228642', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80059656', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(116, '80059657', 'Zolani', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Tanah Laut', '1999-03-15', 27, '25-35', 'Moslem', '22149586285', '0003308555542', '287934541732000', '-', 0, '6301053112990008', 'JL. GANG SAKULA RT 012 RW 004 BATI-BATI BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310020903236', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80059657', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49');
INSERT INTO `employees` (`id`, `nik`, `nama`, `user`, `jumlah`, `jabatan`, `employee_group`, `pers_subarea_text`, `sub_subarea`, `tmk`, `length_of_service`, `gender`, `marital_status`, `birthplace`, `birthdate`, `age`, `range_umur`, `religion`, `jamsostek_id`, `bpjs_id`, `tax_id`, `tax_status`, `number_of_child`, `ktp_nomor`, `ktp_alamat`, `region`, `postal_code`, `no_hp`, `email`, `esgrp_text`, `level`, `position_id`, `org_unit_id`, `org_unit_text`, `pers_subarea_id`, `org_key_id`, `org_key_text`, `bantu_opr_hlp`, `regu_line`, `line`, `regu`, `area_prov_marketing`, `distrik_rayon_marketing`, `vendor_os`, `cost_center`, `cost_center_text`, `emp_status_text`, `excuse`, `tanggal_akhir_kerja`, `pendidikan_level`, `pendidikan_institusi`, `pendidikan_studi`, `ps_type_text`, `status_incentive`, `bank_keys`, `bank_account_no`, `vaksin1_tanggal`, `vaksin1_merk`, `vaksin2_tanggal`, `vaksin2_merk`, `vaksin3_tanggal`, `vaksin3_merk`, `workschedule_id`, `tipe_hari_kerja`, `shift_type`, `no_sidik_jari`, `jam_kerja`, `created_at`, `updated_at`) VALUES
(117, '80059677', 'Riyan Saputra', 'GAS Spv', 1, 'Driver', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Nusa Indah', '1999-11-09', 26, '25-35', 'Moslem', '-', '0002311904801', '-', '-', 0, '6301051511990001', 'JL. A. YANI RT 005 RW 002 NUSA INDAH BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80059677', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(118, '80059679', 'Jumaidi', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Sungai Bakau', '2002-04-13', 24, '<25', 'Moslem', '-', '0000889668832', '-', '-', 0, '6301011304020003', 'JALAN TAMBAK TABANIO RT 021 RW 000 TABANIO TAKISUNG TANAH LAUT', 'Kalimantan Selatan', '70861', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Takisung', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80059679', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(119, '80059680', 'Mizan Sufi', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-01-02', '01 y 06 m', 'Male', 'Single', 'Malabah', '1999-11-15', 26, '25-35', 'Moslem', '-', '0002311904801', '-', '-', 0, '6301051511990001', 'JALAN PADAT KARYA RT 005 RW 000 GUNUNG RAJA TAMBANG ULANG BATI-BATI', 'Kalimantan Selatan', '70854', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Harapan Bangsa', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80059680', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(120, '80060472', 'Ridho Azmi', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-02-01', '01 y 05 m', 'Male', 'Single', 'Banjarmasin', '1997-04-29', 29, '25-35', 'Moslem', '-', '0001966795053', '-', '-', 0, '6371042904970005', 'JL. SUNGAI MIAI DALAM RT 009 RW 000 SUNGAI MIAI BANJARMASIN UTARA BANJARMASIN', 'Kalimantan Selatan', '70123', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMK Harapan Bangsa', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80060472', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(121, '80061272', 'Khairian Ikbal', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-02-01', '01 y 05 m', 'Male', 'Single', 'Tebing Siring', '1997-01-25', 29, '25-35', 'Moslem', '-', '0001904906878', '-', '-', 0, '6301032501970002', 'SUMBER MULYA RT 009 RW 004 SUMBER MULYA PELAIHARI TANAH LAUT', 'Kalimantan Selatan', '70851', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP A', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'A', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Banjarmasin', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80061272', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(122, '80062563', 'Muhammad Idham Halid', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-01', '01 y 02 m', 'Male', 'Marr.', 'Martapura', '1990-07-03', 36, '36-45', 'Moslem', '-', '000012381135', '717978670732000', '-', 0, '6303050307900007', 'JL. BERLIAN NO. 08  RT 003 RW 002 PASAYANGAN SELATAN MARTAPURA BANJAR', 'Kalimantan Selatan', '70619', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'S1', 'Universitas Lambung Mangkurat', 'Ekonomi Akutansi', 'Operative (NI)', 'Non Inc', '-', '0310021420990', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80062563', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(123, '80062960', 'Akhmad Rizaldy Ariskysisam', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Single', 'Tanah Laut', '2003-09-02', 22, '<25', 'Moslem', '-', '0001546236606', '-', '-', 0, '6301050209030001', 'JL. 95 NO. 16  RT 001 RW 001 NUSA INDAH BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310021154540', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80062960', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(124, '80062961', 'Muhammad Mugni Marwa', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Single', 'Tanah Laut', '2006-08-12', 19, '<25', 'Moslem', '-', '0002721946228', '-', '-', 0, '6301051208100002', 'JL. A. YANI\n RT 003 RW 001 PADANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_02', 'WH_Raw Material', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80062961', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(125, '80062962', 'Radi Aditiya', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Single', 'Tanah Laut', '2003-06-14', 23, '<25', 'Moslem', '-', '0001546264809', '-', '-', 0, '6301051406030004', 'JL. A. YANI RT 004 RW 002 LIANG ANGGANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP B', '2030', 'NDL_02_03_02', 'WH_Finish Goods', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '0310021201614', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80062962', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(126, '80062963', 'Robby Maulana', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Marr.', 'Banjarmasin', '1984-01-01', 42, '36-45', 'Moslem', '-', '0001146403989', '725785182731000', '-', 0, '6371050101840018', 'JL. SUNGAI ANDAI NO.138  RT 042 RW 003 SUNGAI ANDAI BANJARMASIN UTARA BANJARMASIN', 'Kalimantan Selatan', '70121', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG IN GROUP A', '2030', 'NDL_02_03_03', 'WH_Finish Goods', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Swadaya Banjarmasin', 'Manajemen Bisnis', 'Operative (NI)', 'Non Inc', '-', '0310021490159', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80062963', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(127, '80062964', 'Muhammad Hipzi Anwari', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Single', 'Cempaka', '2001-07-15', 25, '25-35', 'Moslem', '-', '0000902170034', '-', '-', 0, '6372031507010001', 'JL. MISTAR COKRO KUSUMO RT 014 RW 005 KELURAHAN CEMPAKA KECAMATAN CEMPAKA KOTA BANJARBARU\n RT 014 RW 005  CEMPAKA  CEMPAKA BANJARBARU', 'Kalimantan Selatan', '70733', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMP', 'PKBM Banua Banjar', 'Umum', 'Operative (NI)', 'Non Inc', '-', '0310021491025', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80062964', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(128, '80062965', 'M. Abdul Faqeh', 'WHS Spv', 1, 'Whs FG Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-05-15', '01 y 02 m', 'Male', 'Marr.', 'Paku Alam', '1995-04-12', 31, '25-35', 'Moslem', '-', '0003757035857', '630998060711000', '-', 0, '6303041204950001', 'DS. BINJAI RT 001 RW 000 BINJAI MUARA UYA TABALONG', 'Kalimantan Selatan', '71573', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS FG OUT', '2030', 'NDL_02_03_03', 'WH_Finish Goods', '-', '-', '-', '-', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SKB Bina Ilmu (Paket C)', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80062965', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(129, '80062966', 'Ahmad Ridani Rizki', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2025-05-14', '01 y 02 m', 'Male', 'Single', 'Tanah Laut', '2006-01-24', 20, '<25', 'Moslem', '-', '0002722053622', '-', '-', 0, '6301041101060001', 'TAMBAK KARYA RT 001 RW 001 TAMBAK KARYA KURAU TANAH LAUT', 'Kalimantan Selatan', '70853', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'Keagamaan', 'Operative (NI)', 'Non Inc', '-', '0310020987502', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80062966', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(130, '80062967', 'Valentino Pramudya', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-08-15', '00 y 11 m', 'Male', 'Single', 'Blitar', '2006-05-14', 20, '<25', 'Moslem', '-', '0002734449153', '-', '-', 0, '3572031405060001', 'JL. A. YANI KM 32 KOMPLEK GRIYA ADELIN UTAMA NO. B-5 RT 004 RW 003 LIANG ANGGANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS PAPER CUP', '2030', 'NDL_02_03_08', 'WH_Paper Cup & Garpu', '-', '-', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2834126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK PGRI Banjarbaru', 'Teknik Otomotif', 'Operative (NI)', 'Non Inc', '-', '0310021336089', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80062967', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(131, '80063825', 'Muhammad Rizky Faddila', 'WHS Spv', 1, 'Operator Forklift', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-06-01', '01 y 01 m', 'Male', 'Single', 'Landasan Ulin', '2002-08-06', 23, '<25', 'Moslem', '-', '0002233730608', '-', '-', 0, '6372040608020001', 'JL. KURNIA RT 004 RW 000 LIANG ANGGANG UTARA LIANG ANGGANG BANJARBARU', 'Kalimantan Selatan', '70724', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM', '2030', 'NDL_02_03_02', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 2 Banjarbaru', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80063825', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(132, '80064679', 'Fendy Firdaus', 'GAS Spv', 1, 'Driver', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-07-01', '01 y 00 m', 'Male', 'Single', 'Pematang Panjang', '1998-02-03', 28, '25-35', 'Moslem', '-', '0001970590149', '-', '-', 0, '6303040302980003', 'JL. PEMATANG PANJANG RT 001 RW 000 PEMATANG PANJANG SUNGAI TABUK KAB BANJAR', 'Kalimantan Selatan', '70653', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMKN 1 Gambut', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80064679', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(133, '80065068', 'Akhmad Rijalul Ghani', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-08-04', '00 y 11 m', 'Male', 'Single', 'Tanah Laut', '2003-06-03', 23, '<25', 'Moslem', '-', '0002721963947', '-', '-', 0, '6301050306030002', 'Jl. A .YANI KM 07 RT 002 RW 001 PANDAHAN BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '65542', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP B', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'B', '-', '-', 'PT Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Pandahan', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80065068', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(134, '80065342', 'Siti Bulqis', 'GAS Spv', 1, 'Office Girl', 'Outsourcing', 'ADM HR', 'Non Produksi', '2025-08-01', '00 y 11 m', 'Female', 'Single', 'Tanah Laut', '2006-05-19', 20, '<25', 'Moslem', '-', '0002721958018', '-', '-', 0, '6301055905060001', 'JL A YANI KM 29 DESA PANDAHAN  RT 009 RW 003 PANDAHAN BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80065342', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(135, '80065358', 'Didiyanor', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-08-18', '00 y 11 m', 'Male', 'Marr.', 'Pandahan', '1994-03-04', 32, '25-35', 'Moslem', '-', '0003396468576', '-', '-', 0, '6301050403940002', 'JL. SEKUMPUL GANG RIDHO RT 016 RW 006 TANJUNG REMA DARAT MARTAPURA BANJAR', 'Kalimantan Selatan', '70613', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS PAPER CUP', '2030', 'NDL_02_03_08', 'WH_Paper Cup & Garpu', '-', '-', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2834126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Pondok Pesantren Mursyidul Amin Gambut', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80065358', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(136, '80065697', 'Yolanda Aprilia Dina', 'Prod Shift Spv (Reg. C)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi C', '2025-09-01', '00 y 10 m', 'Female', 'Marr.', 'Banjarmasin', '1999-04-06', 27, '25-35', 'Moslem', '-', '0001546248756', '-', '-', 0, '6301054604990002', 'JL. A. YANI KM 32  RT 009 RW 001 NUSA INDAH BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80065697', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(137, '80065829', 'Jauhari', 'Prod Shift Spv (Reg. A)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi A', '2025-09-01', '00 y 10 m', 'Male', 'Single', 'Tanah Laut', '2006-02-19', 20, '<25', 'Moslem', '-', '0002721900352', '-', '-', 0, '6301051902060002', 'JL. MURUNG KENANGA RT 005 RW 002 BATI-BATI BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80065829', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(138, '80067195', 'Gusti Sopian Sori', 'Prod Shift Spv (Reg. C)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi C', '2025-11-01', '00 y 08 m', 'Male', 'Wdwer', 'Martapura', '1998-10-11', 27, '25-35', 'Moslem', '-', '0001290361127', '-', '-', 0, '6303151110880003', 'JL. MURUNG DARUSSALAM RT 007 RW 004 ANTASAN SENOR ILIR MARTAPURA TIMUR BANJAR', 'Kalimantan Selatan', '70617', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP C', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMK Negeri 1 Martapura', 'Teknik Komputer & Jaringan', 'Operative (NI)', 'Non Inc', '-', '310021725554', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80067195', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(139, '80069126', 'Muhammad Aidi Rahman', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2025-12-01', '00 y 07 m', 'Male', 'Single', 'Liang Anggang', '2006-01-03', 20, '<25', 'Moslem', '-', '0001896157236', '-', '-', 0, '6372040301060003', 'JL. A YANI KM. 29  RT 000 RW 000 LIANG ANGGANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '71213', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Pondok Pesantren Al Mursyidul Amin', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80069126', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(140, '80069721', 'Elvasza Risza Iskandari', 'Prod Shift Spv (Reg. B)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi B', '2026-01-01', '00 y 06 m', 'Female', 'Single', 'Tanah Laut', '2007-05-27', 19, '<25', 'Moslem', '-', '0002721902128', '-', '-', 0, '6301056705070002', 'JL. A. YANI RT. 003 RW. 002 PADANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80069721', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(141, '80069722', 'Muhammad Reyhan Firdaus', 'WHS Spv', 1, 'Whs RM Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2026-01-01', '00 y 06 m', 'Male', 'Single', 'Banjarmasin', '2007-06-07', 19, '<25', 'Moslem', '-', '0001546245843', '-', '-', 0, '6301050706070002', 'JL. UBUDIYAH RT. 001 RW. 004 BATI-BATI BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM SERAH TERIMA', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', 'A', '-', '-', 'PT. Fadanara Berkah Bersama', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPA', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80069722', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(142, '80069724', 'Muhammad Syafii', 'GAS Spv', 1, 'Cleaning Service', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-01-09', '00 y 06 m', 'Male', 'Marr.', 'Pelaihari', '1997-07-07', 29, '25-35', 'Moslem', '-', '0002891781977', '-', '-', 0, '6301052107970002', 'LIANG ANGGANG RT. 004 RW. 001 LIANG ANGGANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80069724', '8 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(143, '80069725', 'Ahmad Suhada', 'WHS Spv', 1, 'Operator Skid Loader', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2026-01-05', '00 y 06 m', 'Male', 'Single', 'Martapura', '2001-05-02', 25, '25-35', 'Moslem', '-', '0001546725183', '-', '-', 0, '6303050205010007', 'DESA NUSA INDAH RT. 010 RW. 002 NUSA INDAH BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS RM UTILITY', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80069725', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(144, '80070134', 'Muniroh', 'Prod Shift Spv (Reg. B)', 1, 'Helper Kupas', 'Outsourcing', 'MFG Production', 'Produksi B', '2026-01-01', '00 y 06 m', 'Female', 'Widow.', 'Bati-Bati', '1996-08-10', 29, '25-35', 'Moslem', '-', '0001545426549', '-', '-', 0, '6301054102940005', 'JL. NUSA INDAH RT 009 RW 001 NUSA INDAH BATI BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80070134', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(145, '80070214', 'Rizky Permana Sukma', 'IR Spv', 1, 'Security Crew', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-02-01', '00 y 05 m', 'Male', 'Marr.', 'Marabahan', '1998-02-18', 28, '25-35', 'Moslem', '-', '0002750921886', '-', '-', 0, '6304151802980001', 'DESA UJUNG RT 002 RW 001 UJUNG BATI- BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'SECURITY GROUP D', '1040', 'NDL_01_03_05', 'HR_Security', '-', '-', '-', 'D', '-', '-', 'PT. Garda Bhakti Nusantara', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Kalsel', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '5. Shift - Regu D', '80070214', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(146, '80073727', 'M.Ridho Alfasha', 'WHS Spv', 1, 'Whs Scrap Helper', 'Outsourcing', 'MFG Warehouse', 'Non Produksi', '2026-03-09', '00 y 04 m', 'Male', 'Single', 'Asam Asam', '1998-05-21', 28, '25-35', 'Moslem', '-', '0002218317006', '-', '-', 0, '6301022105980001', 'JL. A. YANI KM. 118 RT 010 RW 003 ASAM ASAM JORONG TANAH LAUT', 'Kalimantan Selatan', '70814', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'WHS SCRAP', '2030', 'NDL_02_03_03', 'WH_Raw Material', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813126000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'PKBM Serumpun', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80073727', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:20:49'),
(147, '80075445', 'M. Ilham', 'Prod Shift Spv (Reg. B)', 1, 'Helper Serah Terima FG', 'Outsourcing', 'MFG Production', 'Produksi B', '2026-05-01', '00 y 02 m', 'Male', 'Single', 'Bati-Bati', '2007-02-19', 19, '<25', 'Moslem', '-', '0002721805593', '-', '-', 0, '6301051902070003', 'JL. MELATI  RT 010 RW 003 PADANG BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP B', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind B', '-', 'B', '-', '-', 'PT. Fadanara Berkah Bersama', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'IPA', 'Operative (NI)', 'Non Inc', '-', '0310023739652', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '3. Shift - Regu B', '80075445', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(148, '80075934', 'Sub\'Hani', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-05-09', '00 y 02 m', 'Male', 'Single', 'Tanah Laut', '2000-12-01', 25, '25-35', 'Moslem', '-', '0002944208621', '-', '-', 0, '6301051112000006', 'JL. A YANI RT 008 RW 003 PADANG BATI - BATI BANJARMASIN', 'Kalimantan Selatan', '70653', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80075934', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(149, '80075935', 'Khairi Ridhoni', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-05-09', '00 y 02 m', 'Male', 'Single', 'Banjarmasin', '2003-05-17', 23, '<25', 'Moslem', '-', '0001166050686', '-', '-', 0, '6371051705030005', 'JL. PEKAPURAN LAUT NO 8 RT 019  RW 002 PEKAPURAN LAUT BANJARMASIN TENGAH BANJARMASIN', 'Kalimantan Selatan', '70117', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'SMK Negeri 5 Banjarmasin', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80075935', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(150, '80075936', 'Muhammad Ikbal', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-05-09', '00 y 02 m', 'Male', 'Single', 'Tanah Laut', '2008-03-11', 18, '<25', 'Moslem', '-', '0001546472068', '-', '-', 0, '6301051103080002', 'DESA LIANG ANGGANG RT 002 RW 001 LIANG ANGGANG BATI - BATI BANJARMASIN', 'Kalimantan Selatan', '70653', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Warehouse', 'Active', '-', NULL, 'SMA', 'Ponpes Darusallim', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80075936', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(151, '80075940', 'Muhammad Hefni Nafarin', 'Prod Shift Spv (Reg. C)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi C', '2026-05-01', '00 y 02 m', 'Male', 'Single', 'Tanah Laut', '2006-09-04', 19, '<25', 'Moslem', '-', '0001551223574', '-', '-', 0, '6372040409060001', 'JL. TANGGUL UJUNG RT 008 RW. 004 UJUNG BATI - BATI BANJARMASIN', 'Kalimantan Selatan', '70653', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT PAPER CUP C', '2020', 'NDL_02_02_12', 'PR_Paper Cup & Garpu Indirect', '-', 'Ind C', '-', 'C', '-', '-', 'PT Tropis Service', '2834122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'SMA Negeri 1 Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '4. Shift - Regu C', '80075940', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(152, '80075945', 'Muhammad Rafie', 'Prod Shift Spv (Reg. A)', 1, 'Helper Sanitasi', 'Outsourcing', 'MFG Production', 'Produksi A', '2026-05-01', '00 y 02 m', 'Male', 'Single', 'Tanah Laut', '2006-08-17', 19, '<25', 'Moslem', '-', '0002071839251', '-', '-', 0, '6301051708030001', 'JL. SIROTOL MUSTAQIM RT 005 RW 001 BENUA RAYA BATI - BATI BANJARMASIN', 'Kalimantan Selatan', '70653', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'PRD INDIRECT GROUP A', '2020', 'NDL_02_02_02', 'PR_Indirect', '-', 'Ind A', '-', 'A', '-', '-', 'PT Tropis Service', '2813122000', 'PROD - INDIRECT', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'IPS', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '2. Shift - Regu A', '80075945', '7 Jam', '2026-10-05 17:24:03', '2026-10-05 23:19:51'),
(153, '80077374', 'Jainah', 'GAS Spv', 1, 'Office Girl', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-06-01', '00 y 01 m', 'Female', 'Widow.', 'Pandahan', '1974-06-08', 51, '46-55', 'Moslem', '-', '0001544863757', '-', '-', 0, '6301054806740004', 'JL. TALOK SELONG  RT 005 RW 002 PANDAHAN BATI-BATI TANAH LAUT', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMP', 'Madrasah Tsanawiyah Negeri Kurau', 'General', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '5 HK', '1. Non Shift', '80077374', '8 Jam', '2026-10-05 17:24:04', '2026-10-05 23:19:52'),
(154, '80077375', 'Sholehin', 'GAS Spv', 1, 'Sanitasi Lingk & Gardening', 'Outsourcing', 'ADM HR', 'Non Produksi', '2026-06-01', '00 y 01 m', 'Male', 'Single', 'Tanah Laut', '2003-07-25', 22, '<25', 'Moslem', '-', '0002254761538', '-', '-', 0, '6372042305080001', 'JL. JURUSAN PELAIHARI RT 002 RW 001 LANDASAN ULIN SELATAN LIANG ANGGANG', 'Kalimantan Selatan', '70852', '-', '-', 'Operative (NI)', 'Outsourcing', '-', '-', 'EMPLOYEE SERVICES', '1040', 'NDL_01_03_03', 'HR_General Affair Services', '-', '-', '-', '-', '-', '-', 'PT Tropis Service', '2813331000', 'Gen. Affair Services', 'Active', '-', NULL, 'SMA', 'Madrasah Aliyah Ubudiyah Bati-Bati', 'Umum', 'Operative (NI)', 'Non Inc', '-', '-', NULL, '-', NULL, '-', NULL, '-', '-', '6 HK', '1. Non Shift', '80077375', '7 Jam', '2026-10-05 17:24:04', '2026-10-05 23:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `employee_schedules`
--

CREATE TABLE `employee_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Merujuk pada NIK Karyawan',
  `shift_id` bigint UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL COMMENT 'Tanggal penugasan shift',
  `hari_kerja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Reguler' COMMENT 'Jenis jadwal: Reguler, Jadwal Khusus, Tidak Normal, Per Tanggal',
  `special_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan khusus untuk jadwal tanggal tertentu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shift_1_id` bigint UNSIGNED DEFAULT NULL,
  `shift_2_id` bigint UNSIGNED DEFAULT NULL,
  `shift_3_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lembur`
--

CREATE TABLE `lembur` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `is_libur` tinyint(1) NOT NULL DEFAULT '0',
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `istirahat` decimal(8,2) NOT NULL DEFAULT '0.00',
  `jumlah_jam` decimal(8,2) NOT NULL,
  `jam_1_5` decimal(8,2) NOT NULL DEFAULT '0.00',
  `jam_2` decimal(8,2) NOT NULL DEFAULT '0.00',
  `jam_3` decimal(8,2) NOT NULL DEFAULT '0.00',
  `jam_4` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_akumulasi_lembur` decimal(8,2) NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_05_041239_create_attendances_table', 1),
(5, '2026_03_05_042240_add_nama_karyawan_assorting_to_attendances_table', 1),
(6, '2026_03_06_024847_create_employees_table', 1),
(7, '2026_03_06_072605_create_lembur_table', 1),
(8, '2026_03_10_020624_create_stationeries_table', 1),
(9, '2026_03_10_032704_create_stationery_transactions_table', 1),
(10, '2026_03_17_015949_create_pengadaans_table', 1),
(11, '2026_03_26_033942_add_profile_pic_to_users_table', 1),
(12, '2026_04_30_073715_add_is_active_to_stationeries_table', 1),
(13, '2026_05_12_023032_add_google_id_to_users_table', 1),
(14, '2026_05_12_060444_add_photo_and_note_to_attendances_table', 1),
(15, '2026_05_20_000001_create_spls_table', 1),
(16, '2026_05_20_000002_create_spl_details_table', 1),
(17, '2026_05_26_060358_create_shifts_table', 1),
(18, '2026_05_26_060359_create_employee_schedules_table', 1),
(19, '2026_06_03_000000_add_schedule_type_and_note_to_employee_schedules_table', 1),
(20, '2026_06_04_000001_add_shift_1_2_3_to_employee_schedules_table', 1),
(21, '2026_06_04_032619_add_hari_kerja_to_employee_schedules', 1),
(22, '2026_06_19_023004_create_closing_periodes_table', 1),
(23, '2026_10_02_000001_add_signatures_to_spls_table', 2),
(24, '2026_10_06_100000_create_penilaians_table', 3),
(25, '2026_10_06_140000_add_user_to_employees_table', 4),
(26, '2026_10_15_065503_add_nik_to_users_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengadaans`
--

CREATE TABLE `pengadaans` (
  `id` bigint UNSIGNED NOT NULL,
  `stationery_id` bigint UNSIGNED NOT NULL,
  `no_pr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_pr` date NOT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `tgl_released_pr` date DEFAULT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_item` int NOT NULL,
  `qty_po` int DEFAULT NULL,
  `status` enum('Menunggu','Selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penilaians`
--

CREATE TABLE `penilaians` (
  `id` bigint UNSIGNED NOT NULL,
  `bulan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_os` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pers_subarea_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skor_disiplin` int NOT NULL DEFAULT '0',
  `skor_kualitas` int NOT NULL DEFAULT '0',
  `skor_produktivitas` int NOT NULL DEFAULT '0',
  `skor_kerjasama` int NOT NULL DEFAULT '0',
  `skor_tanggung_jawab` int NOT NULL DEFAULT '0',
  `aspek_scores` json DEFAULT NULL,
  `total_skor` int NOT NULL DEFAULT '0',
  `dipertahankan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LfJ9PdyPFVP44phXm2lQEhQIHbrm5yJue7xJ8BxP', 1, '127.0.0.1', 'Symfony', 'YTo1OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NjoiX3Rva2VuIjtzOjQwOiJPMm45bXFJc1dMSWJqMFJ3SDQzSUtZUkdBWjVKdThMMjBZVHU4ODQxIjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDZiYmQ0OGYxZTE4NTE2YjdlMmNiNjZlZWQ5NzdlNWFkNzljODFiNDFlNThlNTdkOTRhMGE3NjQ0N2FmMTdkOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3QvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792051343),
('PCbyGiYP26fbeSBdvMouLWlMNCpNnljiD2vetoNp', 1, '127.0.0.1', 'Symfony', 'YTo1OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NjoiX3Rva2VuIjtzOjQwOiI4elM3UTRzZHZCN2hWVGFWNlhXVnc1SnBwRm1wRnBjS3FHN3U3WTlnIjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDZiYmQ0OGYxZTE4NTE2YjdlMmNiNjZlZWQ5NzdlNWFkNzljODFiNDFlNThlNTdkOTRhMGE3NjQ0N2FmMTdkOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3QvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792049745),
('pYrYbyfe7JFM5hf9k3nrEzKdqutjnYkDpcHKseFL', 6, '127.0.0.1', 'Symfony', 'YTo0OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O3M6NjoiX3Rva2VuIjtzOjQwOiJnTFhiNHhqMUVQcGtFM3hReGRYUmVDb0FEVXhYTTJXMFhXTmpVRnNHIjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiOGI1MzM4ZmI4OTMwYWJjYzMzNTlhN2JkMjQ2OTc5NmViNWQ4MDlkNjYyZDhkYjQ1NDM5ZjMyMzVlNGRmODAyNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1792049784),
('qjUKPiC0AGWKvrqXES3ZFmY73TtD2F7uofLKUOWi', 6, '127.0.0.1', 'Symfony', 'YTo1OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O3M6NjoiX3Rva2VuIjtzOjQwOiJFNzhsV2tKMkZmVTJUeWloTVVoZEM1VDFjYzRmbDZtMDZRMFE2YjZqIjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiOGI1MzM4ZmI4OTMwYWJjYzMzNTlhN2JkMjQ2OTc5NmViNWQ4MDlkNjYyZDhkYjQ1NDM5ZjMyMzVlNGRmODAyNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3QvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792049723),
('sBZUVBKWNaEIqpb7BRWIHCYSTC16B8pwLiI3HnMc', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoid0dDdjNZNVVMa2NxeXNPR21xWkh0TFJuaXprVFlDcWNrejRNenZQaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDZiYmQ0OGYxZTE4NTE2YjdlMmNiNjZlZWQ5NzdlNWFkNzljODFiNDFlNThlNTdkOTRhMGE3NjQ0N2FmMTdkOSI7fQ==', 1792142472),
('SSMY5I8Q1OPVw660OtQRa5e5K0YE5arqA9DwlHr4', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOEJFd2EycVpHYW56ZzdOdHdYQklhbmJOZU5TQURBd3U4SURKRkwyRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDZiYmQ0OGYxZTE4NTE2YjdlMmNiNjZlZWQ5NzdlNWFkNzljODFiNDFlNThlNTdkOTRhMGE3NjQ0N2FmMTdkOSI7fQ==', 1792055574);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `wst_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wst_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `is_overnight` tinyint(1) NOT NULL DEFAULT '0',
  `break1_start` time DEFAULT NULL,
  `break1_end` time DEFAULT NULL,
  `break1_duration` int NOT NULL DEFAULT '0',
  `break2_start` time DEFAULT NULL,
  `break2_end` time DEFAULT NULL,
  `break2_duration` int NOT NULL DEFAULT '0',
  `group_ws_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `wst_code`, `wst_text`, `time_start`, `time_end`, `is_overnight`, `break1_start`, `break1_end`, `break1_duration`, `break2_start`, `break2_end`, `break2_duration`, `group_ws_type`, `created_at`, `updated_at`) VALUES
(1, 'OFF', 'OFF 5HK', '00:00:00', '00:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(2, 'OFF6', 'OFF 6HK', '00:00:00', '00:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(3, 'HLD5', 'Hari Libur Nasional 5HK', '00:00:00', '00:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(4, 'HLD6', 'Hari Libur Nasional 6HK', '00:00:00', '00:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(5, 'HLDP', 'Hari Libur Nasional Sabtu', '00:00:00', '00:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(6, 'P103', 'PG 04.00-11.30', '04:00:00', '11:30:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(7, 'P104', 'PG 04.30-12.00', '04:30:00', '12:00:00', 0, '11:30:00', '12:00:00', 30, NULL, NULL, 0, 'BP07', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(8, 'P105', 'PG 04.30-12.00', '04:30:00', '12:00:00', 0, '11:30:00', '12:00:00', 30, NULL, NULL, 0, 'BP07', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(9, 'PG42', 'PG 05.00-12.30', '05:00:00', '12:30:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(10, 'PG48', 'PG 05.00-10.00', '05:00:00', '10:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(11, 'PG49', 'PG 05.00-12.00', '05:00:00', '12:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(12, 'PG50', 'PG 05.00-12.30', '05:00:00', '12:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(13, 'PG51', 'PG 05.00-14.00', '05:00:00', '14:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(14, 'PG52', 'PG 05.15-12.45', '05:15:00', '12:45:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(15, 'P106', 'PG 05.15-12.45', '05:15:00', '12:45:00', 0, '10:00:00', '10:30:00', 30, NULL, NULL, 0, 'BP09', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(16, 'P107', 'PG 05.15-12.45', '05:15:00', '12:45:00', 0, '11:00:00', '12:00:00', 60, NULL, NULL, 0, 'BP08', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(17, 'P108', 'PG 05.15-10.45', '05:15:00', '10:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(18, 'PG01', 'PG 05.30-14.30', '05:30:00', '14:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(19, 'PG53', 'PG 05.30-10.30', '05:30:00', '10:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(20, 'PG54', 'PG 05.30-11.00', '05:30:00', '11:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(21, 'PG55', 'PG 05.30-11.00', '05:30:00', '11:00:00', 0, '10:00:00', '10:30:00', 30, NULL, NULL, 0, 'BP09', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(22, 'PG56', 'PG 05.30-12.30', '05:30:00', '12:30:00', 0, '10:00:00', '10:30:00', 30, NULL, NULL, 0, 'BP09', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(23, 'PG57', 'PG 05.30-13.00', '05:30:00', '13:00:00', 0, '10:00:00', '10:30:00', 30, NULL, NULL, 0, 'BP09', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(24, 'PG58', 'PG 05.30-13.00', '05:30:00', '13:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(25, 'PG59', 'PG 05.30-13.30', '05:30:00', '13:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(26, 'PG60', 'PG 05.30-13.30', '05:30:00', '13:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(27, 'P109', 'PG 05.30-13.00', '05:30:00', '13:00:00', 0, '11:00:00', '12:00:00', 60, NULL, NULL, 0, 'BP08', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(28, 'PG61', 'PG 05.45-10.45', '05:45:00', '10:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(29, 'PG62', 'PG 05.45-13.15', '05:45:00', '13:15:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(30, 'PG27', 'PG 06.00-13.30', '06:00:00', '13:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(31, 'PG63', 'PG 06.00-11.00', '06:00:00', '11:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(32, 'PG64', 'PG 06.00-11.30', '06:00:00', '11:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(33, 'PG65', 'PG 06.00-12.00', '06:00:00', '12:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:31'),
(34, 'PG66', 'PG 06.00-13.00', '06:00:00', '13:00:00', 0, '10:30:00', '11:00:00', 30, NULL, NULL, 0, 'BP10', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(35, 'PG67', 'PG 06.00-13.30', '06:00:00', '13:30:00', 0, '10:30:00', '11:00:00', 30, NULL, NULL, 0, 'BP10', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(36, 'PG68', 'PG 06.00-13.30', '06:00:00', '13:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(37, 'PG69', 'PG 06.00-14.00', '06:00:00', '14:00:00', 0, '10:30:00', '11:00:00', 30, NULL, NULL, 0, 'BP10', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(38, 'PG70', 'PG 06.00-14.00', '06:00:00', '14:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(39, 'PG71', 'PG 06.00-14.00', '06:00:00', '14:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(40, 'PG72', 'PG 06.00-15.00', '06:00:00', '15:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(41, 'PG73', 'PG 06.15-11.15', '06:15:00', '11:15:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(42, 'PG74', 'PG 06.15-11.45', '06:15:00', '11:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(43, 'PG75', 'PG 06.15-13.45', '06:15:00', '13:45:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(44, 'PG76', 'PG 06.15-13.45', '06:15:00', '13:45:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(45, 'PG77', 'PG 06.15-14.15', '06:15:00', '14:15:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(46, 'PG81', 'PG 06.30-14.30', '06:30:00', '14:30:00', 0, '11:30:00', '12:30:00', 60, NULL, NULL, 0, 'BP06', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(47, 'PG02', 'PG 06.30-11.30', '06:30:00', '11:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(48, 'PG03', 'PG 06.30-14.00', '06:30:00', '14:00:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(49, 'PG04', 'PG 06.30-14.30', '06:30:00', '14:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(50, 'PG28', 'PG 06.30-14.00', '06:30:00', '14:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(51, 'PG29', 'PG 06.30-15.00', '06:30:00', '15:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(52, 'PG78', 'PG 06.30-12.00', '06:30:00', '12:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(53, 'PG79', 'PG 06.30-13.30', '06:30:00', '13:30:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(54, 'PG80', 'PG 06.30-14.00', '06:30:00', '14:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(55, 'PG82', 'PG 06.30-15.30', '06:30:00', '15:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(56, 'PG43', 'PG 06.45-14.15', '06:45:00', '14:15:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(57, 'PG44', 'PG 06.45-11.45', '06:45:00', '11:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(58, 'PG45', 'PG 06.45-14.45', '06:45:00', '14:45:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(59, 'PG83', 'PG 06.45-12.15', '06:45:00', '12:15:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(60, 'PG84', 'PG 06.45-14.15', '06:45:00', '14:15:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(61, 'PG85', 'PG 06.50-11.50', '06:50:00', '11:50:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(62, 'PG86', 'PG 06.50-12.20', '06:50:00', '12:20:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(63, 'PG87', 'PG 06.50-14.20', '06:50:00', '14:20:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(64, 'PG88', 'PG 06.50-14.20', '06:50:00', '14:20:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(65, 'PG89', 'PG 06.55-11.55', '06:55:00', '11:55:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(66, 'PG90', 'PG 06.55-12.25', '06:55:00', '12:25:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(67, 'PG91', 'PG 06.55-14.25', '06:55:00', '14:25:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(68, 'PG92', 'PG 06.55-14.25', '06:55:00', '14:25:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(69, 'PG05', 'PG 07.00-12.00', '07:00:00', '12:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(70, 'PG06', 'PG 07.00-13.00', '07:00:00', '13:00:00', 0, '09:00:00', '10:00:00', 60, NULL, NULL, 0, 'BP02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(71, 'PG07', 'PG 07.00-14.30', '07:00:00', '14:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(72, 'PG08', 'PG 07.00-15.00', '07:00:00', '15:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(73, 'PG09', 'PG 07.00-16.00', '07:00:00', '16:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(74, 'PG30', 'PG 07.00-12.30', '07:00:00', '12:30:00', 0, '11:30:00', '12:00:00', 30, NULL, NULL, 0, 'BP07', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(75, 'PG31', 'PG 07.00-13.00', '07:00:00', '13:00:00', 0, '11:30:00', '12:30:00', 60, NULL, NULL, 0, 'BP06', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(76, 'PG32', 'PG 07.00-13.00', '07:00:00', '13:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(77, 'PG33', 'PG 07.00-15.30', '07:00:00', '15:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(78, 'PG93', 'PG 07.00-12.30', '07:00:00', '12:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(79, 'PG94', 'PG 07.00-12.30', '07:00:00', '12:30:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(80, 'PG95', 'PG 07.00-14.30', '07:00:00', '14:30:00', 0, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(81, 'PG96', 'PG 07.00-14.30', '07:00:00', '14:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(82, 'PG10', 'PG 07.30-12.30', '07:30:00', '12:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(83, 'PG11', 'PG 07.30-13.00', '07:30:00', '13:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(84, 'PG12', 'PG 07.30-15.00', '07:30:00', '15:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(85, 'PG13', 'PG 07.30-15.30', '07:30:00', '15:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(86, 'PG34', 'PG 07.30-16.00', '07:30:00', '16:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(87, 'PG38', 'PG 07.30-16.30', '07:30:00', '16:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(88, 'P110', 'PG 07.30-15.30', '07:30:00', '15:30:00', 0, '13:00:00', '14:00:00', 60, NULL, NULL, 0, 'BP12', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(89, 'PG14', 'PG 08.00-14.00', '08:00:00', '14:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(90, 'PG15', 'PG 08.00-15.30', '08:00:00', '15:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(91, 'PG16', 'PG 08.00-16.00', '08:00:00', '16:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(92, 'PG17', 'PG 08.00-17.00', '08:00:00', '17:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(93, 'PG35', 'PG 08.00-13.00', '08:00:00', '13:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(94, 'PG36', 'PG 08.00-16.30', '08:00:00', '16:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(95, 'PG39', 'PG 08.00-16.00', '08:00:00', '16:00:00', 0, '11:00:00', '12:00:00', 60, NULL, NULL, 0, 'BP08', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(96, 'PG97', 'PG 08.00-12.00', '08:00:00', '12:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(97, 'PG98', 'PG 08.00-13.00', '08:00:00', '13:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(98, 'PG99', 'PG 08.00-13.30', '08:00:00', '13:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(99, 'P100', 'PG 08.00-17.00', '08:00:00', '17:00:00', 0, '12:30:00', '13:30:00', 60, NULL, NULL, 0, 'BP11', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(100, 'P101', 'PG 08.00-17.00', '08:00:00', '17:00:00', 0, '13:00:00', '14:00:00', 60, NULL, NULL, 0, 'BP12', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(101, 'PG40', 'PG 08.15-16.45', '08:15:00', '16:45:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(102, 'PG41', 'PG 08.15-17.15', '08:15:00', '17:15:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(103, 'PG18', 'PG 08.30-14.30', '08:30:00', '14:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(104, 'PG19', 'PG 08.30-16.30', '08:30:00', '16:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(105, 'PG20', 'PG 08.30-17.30', '08:30:00', '17:30:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(106, 'P111', 'PG 08.30-14.00', '08:30:00', '14:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(107, 'PG21', 'PG 09.00-18.00', '09:00:00', '18:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(108, 'PG25', 'PG 09.00-17.00', '09:00:00', '17:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(109, 'PG26', 'PG 09.00-15.00', '09:00:00', '15:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(110, 'P102', 'PG 09.00-14.00', '09:00:00', '14:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(111, 'PG37', 'PG 09.30-18.00', '09:30:00', '18:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(112, 'PG22', 'PG 10.00-15.30', '10:00:00', '15:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(113, 'PG23', 'PG 10.00-17.30', '10:00:00', '17:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(114, 'PG24', 'PG 10.00-18.00', '10:00:00', '18:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(115, 'SG22', 'SG 10.00-16.00', '10:00:00', '16:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(116, 'SG29', 'SG 10.00-18.30', '10:00:00', '18:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(117, 'SG30', 'SG 10.00-19.00', '10:00:00', '19:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(118, 'SG41', 'SG 10.00-15.00', '10:00:00', '15:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(119, 'SG42', 'SG 10.30-16.00', '10:30:00', '16:00:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(120, 'SG81', 'SG 10.45-15.45', '10:45:00', '15:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(121, 'SG01', 'SG 11.00-19.00', '11:00:00', '19:00:00', 0, '17:00:00', '18:00:00', 60, NULL, NULL, 0, 'BS01', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(122, 'SG31', 'SG 11.00-19.30', '11:00:00', '19:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(123, 'SG32', 'SG 11.00-20.00', '11:00:00', '20:00:00', 0, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(124, 'SG43', 'SG 11.00-16.00', '11:00:00', '16:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(125, 'SG44', 'SG 11.00-18.30', '11:00:00', '18:30:00', 0, '14:00:00', '14:30:00', 30, NULL, NULL, 0, 'BS05', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(126, 'SG86', 'SG 11.00-16.30', '11:00:00', '16:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(127, 'SG45', 'SG 11.15-16.15', '11:15:00', '16:15:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(128, 'SG02', 'SG 11.30-16.30', '11:30:00', '16:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(129, 'SG23', 'SG 11.30-20.00', '11:30:00', '20:00:00', 0, '17:00:00', '17:30:00', 30, NULL, NULL, 0, 'BS04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(130, 'SG36', 'SG 11.45-16.45', '11:45:00', '16:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(131, 'SG46', 'SG 11.45-16.45', '11:45:00', '16:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(132, 'SG03', 'SG 12.00-17.00', '12:00:00', '17:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(133, 'SG47', 'SG 12.00-17.30', '12:00:00', '17:30:00', 0, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(134, 'SG48', 'SG 12.00-17.30', '12:00:00', '17:30:00', 0, '17:00:00', '17:30:00', 30, NULL, NULL, 0, 'BS04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(135, 'SG49', 'SG 12.00-19.30', '12:00:00', '19:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(136, 'SG82', 'SG 12.00-21.00', '12:00:00', '21:00:00', 0, '19:00:00', '20:00:00', 60, NULL, NULL, 0, 'BS10', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(137, 'SG50', 'SG 12.15-17.15', '12:15:00', '17:15:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(138, 'SG51', 'SG 12.20-17.20', '12:20:00', '17:20:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(139, 'SG52', 'SG 12.25-17.25', '12:25:00', '17:25:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(140, 'SG24', 'SG 12.30-17.30', '12:30:00', '17:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(141, 'SG83', 'SG 12.45-20.15', '12:45:00', '20:15:00', 0, '16:00:00', '16:30:00', 30, NULL, NULL, 0, 'BS06', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(142, 'SG04', 'SG 13.00-18.00', '13:00:00', '18:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(143, 'SG25', 'SG 13.00-19.00', '13:00:00', '19:00:00', 0, '17:00:00', '18:00:00', 60, NULL, NULL, 0, 'BS01', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(144, 'SG26', 'SG 13.00-21.30', '13:00:00', '21:30:00', 0, '17:00:00', '17:30:00', 30, NULL, NULL, 0, 'BS04', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(145, 'SG33', 'SG 13.00-21.00', '13:00:00', '21:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(146, 'SG53', 'SG 13.00-20.30', '13:00:00', '20:30:00', 0, '16:00:00', '16:30:00', 30, NULL, NULL, 0, 'BS06', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(147, 'SG54', 'SG 13.00-20.30', '13:00:00', '20:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(148, 'SG55', 'SG 13.15-20.45', '13:15:00', '20:45:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(149, 'SG05', 'SG 13.30-21.00', '13:30:00', '21:00:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(150, 'SG56', 'SG 13.30-18.30', '13:30:00', '18:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(151, 'SG57', 'SG 13.30-21.00', '13:30:00', '21:00:00', 0, '16:00:00', '16:30:00', 30, NULL, NULL, 0, 'BS06', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(152, 'SG58', 'SG 13.45-19.15', '13:45:00', '19:15:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(153, 'SG59', 'SG 13.45-21.15', '13:45:00', '21:15:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(154, 'SG06', 'SG 14.00-21.30', '14:00:00', '21:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(155, 'SG27', 'SG 14.00-19.30', '14:00:00', '19:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(156, 'SG34', 'SG 14.00-22.00', '14:00:00', '22:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:32'),
(157, 'SG35', 'SG 14.00-23.00', '14:00:00', '23:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(158, 'SG60', 'SG 14.00-20.00', '14:00:00', '20:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(159, 'SG61', 'SG 14.00-21.30', '14:00:00', '21:30:00', 0, '17:00:00', '17:30:00', 30, NULL, NULL, 0, 'BS04', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(160, 'SG84', 'SG 14.00-23.00', '14:00:00', '23:00:00', 0, '19:00:00', '20:00:00', 60, NULL, NULL, 0, 'BS10', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(161, 'SG37', 'SG 14.15-21.45', '14:15:00', '21:45:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(162, 'SG62', 'SG 14.15-19.45', '14:15:00', '19:45:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(163, 'SG63', 'SG 14.15-21.45', '14:15:00', '21:45:00', 0, '17:30:00', '18:00:00', 30, NULL, NULL, 0, 'BS08', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(164, 'SG64', 'SG 14.20-19.50', '14:20:00', '19:50:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(165, 'SG65', 'SG 14.20-21.50', '14:20:00', '21:50:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(166, 'SG66', 'SG 14.25-19.55', '14:25:00', '19:55:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(167, 'SG67', 'SG 14.25-21.55', '14:25:00', '21:55:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(168, 'SG07', 'SG 14.30-19.30', '14:30:00', '19:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(169, 'SG08', 'SG 14.30-20.00', '14:30:00', '20:00:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(170, 'SG09', 'SG 14.30-22.00', '14:30:00', '22:00:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(171, 'SG10', 'SG 14.30-22.30', '14:30:00', '22:30:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(172, 'SG38', 'SG 14.45-22.15', '14:45:00', '22:15:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(173, 'SG11', 'SG 15.00-21.00', '15:00:00', '21:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(174, 'SG12', 'SG 15.00-22.30', '15:00:00', '22:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(175, 'SG13', 'SG 15.00-23.00', '15:00:00', '23:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(176, 'SG68', 'SG 15.00-20.30', '15:00:00', '20:30:00', 0, '18:30:00', '19:00:00', 30, NULL, NULL, 0, 'BD09', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(177, 'SG69', 'SG 15.00-22.00', '15:00:00', '22:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(178, 'SG85', 'SG 15.00-00.00', '15:00:00', '00:00:00', 1, '19:00:00', '20:00:00', 60, NULL, NULL, 0, 'BS10', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(179, 'SG14', 'SG 15.30-23.00', '15:30:00', '23:00:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(180, 'SG15', 'SG 15.30-23.30', '15:30:00', '23:30:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(181, 'SG16', 'SG 16.00-22.00', '16:00:00', '22:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(182, 'SG17', 'SG 16.00-23.30', '16:00:00', '23:30:00', 0, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(183, 'SG18', 'SG 16.00-23.59', '16:00:00', '23:59:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(184, 'SG70', 'SG 16.00-21.00', '16:00:00', '21:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(185, 'SG28', 'SG 16.30-21.30', '16:30:00', '21:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(186, 'SG71', 'SG 16.30-22.00', '16:30:00', '22:00:00', 0, '18:30:00', '19:00:00', 30, NULL, NULL, 0, 'BD09', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(187, 'SG72', 'SG 16.30-22.30', '16:30:00', '22:30:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(188, 'SG73', 'SG 16.45-21.45', '16:45:00', '21:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(189, 'SG74', 'SG 17.00-23.00', '17:00:00', '23:00:00', 0, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(190, 'SG19', 'SG 17.00-22.00', '17:00:00', '22:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(191, 'SG75', 'SG 17.15-22.15', '17:15:00', '22:15:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(192, 'SG76', 'SG 17.20-22.20', '17:20:00', '22:20:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(193, 'SG77', 'SG 17.25-22.25', '17:25:00', '22:25:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(194, 'SG20', 'SG 17.30-22.30', '17:30:00', '22:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(195, 'SG21', 'SG 17.30-00.30', '17:30:00', '00:30:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(196, 'SG78', 'SG 17.45-22.45', '17:45:00', '22:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(197, 'SG79', 'SG 17.50-22.50', '17:50:00', '22:50:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(198, 'SG80', 'SG 17.55-22.55', '17:55:00', '22:55:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(199, 'MM02', 'MM 18.00-01.00', '18:00:00', '01:00:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(200, 'MM01', 'MM 18.00-23.00', '18:00:00', '23:00:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(201, 'MM03', 'MM 18.00-01.30', '18:00:00', '01:30:00', 1, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(202, 'MM04', 'MM 18.00-02.00', '18:00:00', '02:00:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(203, 'MM21', 'MM 18.00-01.30', '18:00:00', '01:30:00', 1, '00:30:00', '01:00:00', 30, NULL, NULL, 0, 'BM03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(204, 'MM05', 'MM 18.30-23.30', '18:30:00', '23:30:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(205, 'MM06', 'MM 18.30-01.30', '18:30:00', '01:30:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(206, 'MM07', 'MM 18.30-02.30', '18:30:00', '02:30:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(207, 'MM22', 'MM 18.30-02.00', '18:30:00', '02:00:00', 1, '00:30:00', '01:00:00', 30, NULL, NULL, 0, 'BM03', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(208, 'MM37', 'MM 18.45-02.15', '18:45:00', '02:15:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(209, 'MM40', 'MM 18.45-23.45', '18:45:00', '23:45:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(210, 'MM23', 'MM 19.00-00.30', '19:00:00', '00:30:00', 1, '22:30:00', '23:00:00', 30, NULL, NULL, 0, 'BM05', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(211, 'MM24', 'MM 19.00-02.30', '19:00:00', '02:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(212, 'MM42', 'MM 20.00-03.30', '20:00:00', '03:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(213, 'MM44', 'MM 21.00-05.00', '21:00:00', '05:00:00', 1, '00:00:00', '01:00:00', 60, NULL, NULL, 0, 'BM11', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(214, 'MM45', 'MM 21.00-05.00', '21:00:00', '05:00:00', 1, '01:00:00', '02:00:00', 60, NULL, NULL, 0, 'BM07', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(215, 'MM46', 'MM 21.00-06.00', '21:00:00', '06:00:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(216, 'MM47', 'MM 21.00-06.00', '21:00:00', '06:00:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(217, 'MM49', 'MM 21.15-05.45', '21:15:00', '05:45:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(218, 'MM51', 'MM 21.15-06.15', '21:15:00', '06:15:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(219, 'MM08', 'MM 21.30-06.00', '21:30:00', '06:00:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(220, 'MM09', 'MM 21.30-06.30', '21:30:00', '06:30:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(221, 'MM25', 'MM 21.30-03.30', '21:30:00', '03:30:00', 1, '01:30:00', '02:30:00', 60, NULL, NULL, 0, 'BM08', '2026-10-15 01:00:53', '2026-10-15 17:13:33'),
(222, 'MM26', 'MM 21.30-05.00', '21:30:00', '05:00:00', 1, '02:00:00', '02:30:00', 30, NULL, NULL, 0, 'BM09', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(223, 'MM27', 'MM 21.30-05.30', '21:30:00', '05:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(224, 'MM28', 'MM 21.30-06.30', '21:30:00', '06:30:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(225, 'MM54', 'MM 21.30-06.30', '21:30:00', '06:30:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(226, 'MM38', 'MM 21.45-06.45', '21:45:00', '06:45:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(227, 'MM56', 'MM 21.45-06.15', '21:45:00', '06:15:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(228, 'MM58', 'MM 21.45-06.45', '21:45:00', '06:45:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(229, 'MM10', 'MM 22.00-03.00', '22:00:00', '03:00:00', 1, NULL, NULL, 0, NULL, NULL, 0, NULL, '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(230, 'MM11', 'MM 22.00-06.00', '22:00:00', '06:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(231, 'MM12', 'MM 22.00-06.30', '22:00:00', '06:30:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(232, 'MM13', 'MM 22.00-07.00', '22:00:00', '07:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(233, 'MM29', 'MM 22.00-04.00', '22:00:00', '04:00:00', 1, '01:00:00', '02:00:00', 60, NULL, NULL, 0, 'BM07', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(234, 'MM30', 'MM 22.00-06.30', '22:00:00', '06:30:00', 1, '02:00:00', '02:30:00', 30, NULL, NULL, 0, 'BM09', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(235, 'MM63', 'MM 22.00-03.30', '22:00:00', '03:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(236, 'MM66', 'MM 22.00-07.00', '22:00:00', '07:00:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(237, 'MM77', 'MM 22.00-05.30', '22:00:00', '05:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(238, 'MM39', 'MM 22.15-06.45', '22:15:00', '06:45:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(239, 'MM14', 'MM 22.30-07.00', '22:30:00', '07:00:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(240, 'MM31', 'MM 22.30-04.30', '22:30:00', '04:30:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(241, 'MM32', 'MM 22.30-06.30', '22:30:00', '06:30:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(242, 'MM33', 'MM 22.30-07.00', '22:30:00', '07:00:00', 1, '02:00:00', '02:30:00', 30, NULL, NULL, 0, 'BM09', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(243, 'MM67', 'MM 22.30-06.30', '22:30:00', '06:30:00', 1, '02:30:00', '03:30:00', 60, NULL, NULL, 0, 'BM12', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(244, 'MM68', 'MM 22.30-06.30', '22:30:00', '06:30:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(245, 'MM80', 'MM 22.30-06.00', '22:30:00', '06:00:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(246, 'MM15', 'MM 23.00-05.00', '23:00:00', '05:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(247, 'MM16', 'MM 23.00-07.00', '23:00:00', '07:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(248, 'MM17', 'MM 23.00-08.00', '23:00:00', '08:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(249, 'MM34', 'MM 23.00-05.00', '23:00:00', '05:00:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(250, 'MM35', 'MM 23.00-07.00', '23:00:00', '07:00:00', 1, '01:00:00', '02:00:00', 60, NULL, NULL, 0, 'BM07', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(251, 'MM36', 'MM 23.00-07.00', '23:00:00', '07:00:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(252, 'MM69', 'MM 23.00-07.00', '23:00:00', '07:00:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(253, 'MM18', 'MM 23.30-08.00', '23:30:00', '08:00:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(254, 'MM19', 'MM 23.59-06.00', '23:59:00', '06:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(255, 'MM20', 'MM 23.59-08.00', '23:59:00', '08:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 01:00:54', '2026-10-15 17:13:33'),
(256, 'BM11', 'BM 00.00-01.00', '01:00:00', '01:00:00', 0, '00:00:00', '01:00:00', 60, NULL, NULL, 0, 'BM11', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(257, 'BM03', 'BM 00.30-01.00', '01:00:00', '00:30:00', 1, '00:30:00', '01:00:00', 30, NULL, NULL, 0, 'BM03', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(258, 'BM04', 'BM 01.00-01.30', '01:30:00', '00:30:00', 1, '01:00:00', '01:30:00', 30, NULL, NULL, 0, 'BM04', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(259, 'BM07', 'BM 01.00-02.00', '02:00:00', '01:00:00', 1, '01:00:00', '02:00:00', 60, NULL, NULL, 0, 'BM07', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(260, 'BM08', 'BM 01.30-02.30', '02:30:00', '01:00:00', 1, '01:30:00', '02:30:00', 60, NULL, NULL, 0, 'BM08', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(261, 'BM06', 'BM 02.00-03.00', '03:00:00', '01:00:00', 1, '02:00:00', '03:00:00', 60, NULL, NULL, 0, 'BM06', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(262, 'BM09', 'BM 02.00-02.30', '02:30:00', '00:30:00', 1, '02:00:00', '02:30:00', 30, NULL, NULL, 0, 'BM09', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(263, 'BM12', 'BM 02.30-03.30', '03:30:00', '01:00:00', 1, '02:30:00', '03:30:00', 60, NULL, NULL, 0, 'BM12', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(264, 'BM01', 'BM 03.00-03.30', '03:30:00', '00:30:00', 1, '03:00:00', '03:30:00', 30, NULL, NULL, 0, 'BM01', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(265, 'BM02', 'BM 03.00-04.00', '04:00:00', '01:00:00', 1, '03:00:00', '04:00:00', 60, NULL, NULL, 0, 'BM02', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(266, 'BM13', 'BM 04.00-05.00', '05:00:00', '01:00:00', 1, '04:00:00', '05:00:00', 60, NULL, NULL, 0, 'BM13', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(267, 'BP01', 'BP 09.00-09.30', '09:30:00', '00:30:00', 1, '09:00:00', '09:30:00', 30, NULL, NULL, 0, 'BP01', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(268, 'BP02', 'BP 09.00-10.00', '10:00:00', '01:00:00', 1, '09:00:00', '10:00:00', 60, NULL, NULL, 0, 'BP02', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(269, 'BP09', 'BP 10.00-10.30', '10:30:00', '00:30:00', 1, '10:00:00', '10:30:00', 30, NULL, NULL, 0, 'BP09', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(270, 'BP10', 'BP 10.30-11.00', '11:00:00', '00:30:00', 1, '10:30:00', '11:00:00', 30, NULL, NULL, 0, 'BP10', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(271, 'BP03', 'BP 11.00-11.30', '11:30:00', '00:30:00', 1, '11:00:00', '11:30:00', 30, NULL, NULL, 0, 'BP03', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(272, 'BP08', 'BP 11.00-12.00', '12:00:00', '01:00:00', 1, '11:00:00', '12:00:00', 60, NULL, NULL, 0, 'BP08', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(273, 'BP06', 'BP 11.30-12.30', '12:30:00', '01:00:00', 1, '11:30:00', '12:30:00', 60, NULL, NULL, 0, 'BP06', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(274, 'BP07', 'BP 11.30-12.00', '12:00:00', '00:30:00', 1, '11:30:00', '12:00:00', 30, NULL, NULL, 0, 'BP07', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(275, 'BP04', 'BP 12.00-12.30', '12:30:00', '00:30:00', 1, '12:00:00', '12:30:00', 30, NULL, NULL, 0, 'BP04', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(276, 'BP05', 'BP 12.00-13.00', '13:00:00', '01:00:00', 1, '12:00:00', '13:00:00', 60, NULL, NULL, 0, 'BP05', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(277, 'BP11', 'BP 12.30-13.30', '13:30:00', '01:00:00', 1, '12:30:00', '13:30:00', 60, NULL, NULL, 0, 'BP11', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(278, 'BP12', 'BP 13.00-14.00', '14:00:00', '01:00:00', 1, '13:00:00', '14:00:00', 60, NULL, NULL, 0, 'BP12', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(279, 'BS05', 'BS 14.00-14.30', '14:30:00', '00:30:00', 1, '14:00:00', '14:30:00', 30, NULL, NULL, 0, 'BS05', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(280, 'BS06', 'BS 16.00-16.30', '16:30:00', '00:30:00', 1, '16:00:00', '16:30:00', 30, NULL, NULL, 0, 'BS06', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(281, 'BS01', 'BS 17.00-18.00', '18:00:00', '01:00:00', 1, '17:00:00', '18:00:00', 60, NULL, NULL, 0, 'BS01', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(282, 'BS04', 'BS 17.00-17.30', '17:30:00', '00:30:00', 1, '17:00:00', '17:30:00', 30, NULL, NULL, 0, 'BS04', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(283, 'BS08', 'BS 17.30-18.00', '18:00:00', '00:30:00', 1, '17:30:00', '18:00:00', 30, NULL, NULL, 0, 'BS08', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(284, 'BS02', 'BS 18.00-18.30', '18:30:00', '00:30:00', 1, '18:00:00', '18:30:00', 30, NULL, NULL, 0, 'BS02', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(285, 'BS03', 'BS 18.00-19.00', '19:00:00', '01:00:00', 1, '18:00:00', '19:00:00', 60, NULL, NULL, 0, 'BS03', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(286, 'BD09', 'BD 18.30-19.00', '19:00:00', '00:30:00', 1, '18:30:00', '19:00:00', 30, NULL, NULL, 0, 'BD09', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(287, 'BS10', 'BS 19.00-20.00', '20:00:00', '01:00:00', 1, '19:00:00', '20:00:00', 60, NULL, NULL, 0, 'BS10', '2026-10-15 17:11:10', '2026-10-15 17:11:10'),
(288, 'BM05', 'BM 22.30-23.00', '23:00:00', '00:30:00', 1, '22:30:00', '23:00:00', 30, NULL, NULL, 0, 'BM05', '2026-10-15 17:11:10', '2026-10-15 17:11:10');
   
-- --------------------------------------------------------

--
-- Table structure for table `spls`
--

CREATE TABLE `spls` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `dibuat_oleh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ditugaskan_oleh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disetujui_oleh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diketahui_oleh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spls`
--

INSERT INTO `spls` (`id`, `tanggal`, `vendor`, `created_by`, `dibuat_oleh`, `ditugaskan_oleh`, `disetujui_oleh`, `diketahui_oleh`, `created_at`, `updated_at`) VALUES
(1, '2026-10-02', NULL, 1, 'ADMIN SUPER', 'PIC OUTSOURCING', 'DEPT. HEAD USER', 'DEPT. HEAD HR', '2026-10-01 17:11:08', '2026-10-01 19:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `spl_details`
--

CREATE TABLE `spl_details` (
  `id` bigint UNSIGNED NOT NULL,
  `spl_id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_in` time DEFAULT NULL,
  `jam_out` time DEFAULT NULL,
  `lembur_awal` time DEFAULT NULL,
  `lembur_akhir` time DEFAULT NULL,
  `istirahat_awal` time DEFAULT NULL,
  `istirahat_akhir` time DEFAULT NULL,
  `total_jam` decimal(8,2) NOT NULL DEFAULT '0.00',
  `alasan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spl_details`
--

INSERT INTO `spl_details` (`id`, `spl_id`, `nik`, `nama`, `jabatan`, `jam_in`, `jam_out`, `lembur_awal`, `lembur_akhir`, `istirahat_awal`, `istirahat_akhir`, `total_jam`, `alasan`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, '80007073', 'fulan', 'Cleaning Service', '08:00:00', '14:00:00', '14:00:00', '15:00:00', NULL, NULL, '1.00', 'Service', 'Pembersihan Mesin Press Line 7', '2026-10-01 17:11:08', '2026-10-01 19:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `stationeries`
--

CREATE TABLE `stationeries` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok_awal` int NOT NULL DEFAULT '0',
  `stok_masuk` int NOT NULL DEFAULT '0',
  `stok_keluar` int NOT NULL DEFAULT '0',
  `harga_unit` int NOT NULL DEFAULT '0',
  `avg_penggunaan` decimal(8,2) NOT NULL DEFAULT '0.00',
  `lead_time` int NOT NULL DEFAULT '0',
  `safety_stock` int NOT NULL DEFAULT '0',
  `min_stok` int NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stationery_transactions`
--

CREATE TABLE `stationery_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `stationery_id` bigint UNSIGNED NOT NULL,
  `type` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `no_fpb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'karyawan',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `nik`, `email`, `google_id`, `profile_pic`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADMIN SUPER', NULL, 'superadmin@gmail.com', NULL, NULL, NULL, '$2y$12$/2q4gWXCQB.5nYaLO6fscOopxT7tTYWEfN6/ECWnlfvZjqNk7Vk62', 'superadmin', NULL, '2026-10-01 16:53:12', '2026-10-01 16:53:12', NULL),
(2, 'Admin Gudang', NULL, 'adminstok@gmail.com', NULL, NULL, NULL, '$2y$12$xe/4HUJFwhjRLzPL2C9dn.zmZHlv1qHw6dnGbkI1oqfV8CoYdU2OK', 'admin_stationery', NULL, '2026-10-01 16:53:13', '2026-10-01 16:53:13', NULL),
(5, 'Muhammad Jerry Noval Ramadhan', '80027016', 'kiyana@gmail.com', NULL, NULL, NULL, '$2y$12$El/Fbs63skUYwG0PVVFrquRsiZDmLrgfoppWLnAnmoN0nIr1.XTfm', 'karyawan', NULL, '2026-10-01 19:38:28', '2026-10-14 23:24:28', NULL),
(6, 'Mujairimi', '80007045', 'lutfifikar2@gmail.com', '105479746524498202452', NULL, NULL, '$2y$12$dXzr5P4W8NEIkeNxVrgTsuGsP7su/XkzEcQ3roR9z8X8hEdc26XV.', 'karyawan', NULL, '2026-10-05 22:52:37', '2026-10-14 23:24:28', NULL),
(7, 'ADMIN PENILAIAN', NULL, 'adminpenilaian@gmail.com', NULL, NULL, NULL, '$2y$12$j29RCn0slRwJyUjqpKe0Z.Rs9dqHG0McTYFgfQeDmkXLeVlCEAs/y', 'admin_penilaian', NULL, '2026-10-14 22:02:55', '2026-10-14 22:13:10', NULL),
(8, 'hani', '68899998', 'hani@gmail.com', NULL, NULL, NULL, '$2y$12$CMhOtXxQECgrfQ6eK.l.mOt8OH8FuHouB4X8vXe52CBM/P08EUkSK', 'admin_internal', NULL, '2026-10-15 22:19:48', '2026-10-15 22:19:48', NULL),
(9, 'caca', '8767599', 'caca@gmail.com', NULL, NULL, NULL, '$2y$12$Bx6Q2zurMhWHNb6qoW9dgeQd5FzvyqQ3.akpB43D1sHRKqW7b8A32', 'admin_outsourcing', NULL, '2026-10-15 22:20:23', '2026-10-15 22:20:23', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `closing_periodes`
--
ALTER TABLE `closing_periodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `closing_periodes_user_id_foreign` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_schedule_unique` (`nik`,`schedule_date`),
  ADD KEY `employee_schedules_shift_id_foreign` (`shift_id`),
  ADD KEY `employee_schedules_shift_1_id_foreign` (`shift_1_id`),
  ADD KEY `employee_schedules_shift_2_id_foreign` (`shift_2_id`),
  ADD KEY `employee_schedules_shift_3_id_foreign` (`shift_3_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lembur`
--
ALTER TABLE `lembur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lembur_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengadaans`
--
ALTER TABLE `pengadaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengadaans_stationery_id_foreign` (`stationery_id`);

--
-- Indexes for table `penilaians`
--
ALTER TABLE `penilaians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penilaians_nik_bulan_tahun_unique` (`nik`,`bulan`,`tahun`),
  ADD KEY `penilaians_nik_index` (`nik`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_wst_code_unique` (`wst_code`);

--
-- Indexes for table `spls`
--
ALTER TABLE `spls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spls_created_by_foreign` (`created_by`);

--
-- Indexes for table `spl_details`
--
ALTER TABLE `spl_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spl_details_spl_id_foreign` (`spl_id`);

--
-- Indexes for table `stationeries`
--
ALTER TABLE `stationeries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stationeries_kode_unique` (`kode`);

--
-- Indexes for table `stationery_transactions`
--
ALTER TABLE `stationery_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stationery_transactions_stationery_id_foreign` (`stationery_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nik_unique` (`nik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `closing_periodes`
--
ALTER TABLE `closing_periodes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lembur`
--
ALTER TABLE `lembur`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pengadaans`
--
ALTER TABLE `pengadaans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penilaians`
--
ALTER TABLE `penilaians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=289;

--
-- AUTO_INCREMENT for table `spls`
--
ALTER TABLE `spls`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `spl_details`
--
ALTER TABLE `spl_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stationeries`
--
ALTER TABLE `stationeries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stationery_transactions`
--
ALTER TABLE `stationery_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `closing_periodes`
--
ALTER TABLE `closing_periodes`
  ADD CONSTRAINT `closing_periodes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD CONSTRAINT `employee_schedules_shift_1_id_foreign` FOREIGN KEY (`shift_1_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_schedules_shift_2_id_foreign` FOREIGN KEY (`shift_2_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_schedules_shift_3_id_foreign` FOREIGN KEY (`shift_3_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_schedules_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lembur`
--
ALTER TABLE `lembur`
  ADD CONSTRAINT `lembur_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengadaans`
--
ALTER TABLE `pengadaans`
  ADD CONSTRAINT `pengadaans_stationery_id_foreign` FOREIGN KEY (`stationery_id`) REFERENCES `stationeries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `spls`
--
ALTER TABLE `spls`
  ADD CONSTRAINT `spls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `spl_details`
--
ALTER TABLE `spl_details`
  ADD CONSTRAINT `spl_details_spl_id_foreign` FOREIGN KEY (`spl_id`) REFERENCES `spls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stationery_transactions`
--
ALTER TABLE `stationery_transactions`
  ADD CONSTRAINT `stationery_transactions_stationery_id_foreign` FOREIGN KEY (`stationery_id`) REFERENCES `stationeries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
