<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['type_id' => 1, 'name' => 'Laporan Kas Harian'],
            ['type_id' => 1, 'name' => 'Laporan Kas Mingguan'],
            ['type_id' => 1, 'name' => 'Laporan Kas Bulanan'],
            ['type_id' => 2, 'name' => 'Ringkasan Tahunan'],
            ['type_id' => 2, 'name' => 'Laporan Keuangan Akhir Tahun'],
            ['type_id' => 2, 'name' => 'Laporan Rencana Anggaran'],
            ['type_id' => 3, 'name' => 'Laporan Penjualan Produk'],
            ['type_id' => 3, 'name' => 'Laporan Penjualan Layanan'],
            ['type_id' => 3, 'name' => 'Laporan Penjualan Online'],
            ['type_id' => 3, 'name' => 'Laporan Penjualan Offline'],
            ['type_id' => 4, 'name' => 'Inventaris Gedung'],
            ['type_id' => 4, 'name' => 'Inventaris Kendaraan'],
            ['type_id' => 4, 'name' => 'Inventaris Elektronik'],
            ['type_id' => 5, 'name' => 'Laporan Proyek A'],
            ['type_id' => 5, 'name' => 'Laporan Proyek B'],
            ['type_id' => 5, 'name' => 'Laporan Proyek C'],
            ['type_id' => 6, 'name' => 'Audit Keuangan'],
            ['type_id' => 6, 'name' => 'Audit Proses'],
            ['type_id' => 6, 'name' => 'Audit Kepatuhan'],
            ['type_id' => 7, 'name' => 'Notulen Rapat Mingguan'],
            ['type_id' => 7, 'name' => 'Notulen Rapat Bulanan'],
            ['type_id' => 8, 'name' => 'Insiden Keselamatan Kerja'],
            ['type_id' => 8, 'name' => 'Insiden Kesehatan Pegawai'],
            ['type_id' => 9, 'name' => 'Pelatihan Teknis'],
            ['type_id' => 9, 'name' => 'Pelatihan Softskill'],
            ['type_id' => 10, 'name' => 'Evaluasi Karyawan'],
            ['type_id' => 10, 'name' => 'Evaluasi Proyek'],
            ['type_id' => 11, 'name' => 'Produksi Harian'],
            ['type_id' => 11, 'name' => 'Produksi Bulanan'],
            ['type_id' => 12, 'name' => 'Distribusi Regional'],
        ];

        foreach ($categories as $cat) {
            DB::table('report_categories')->insert([
                'type_id' => $cat['type_id'],
                'name' => $cat['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}