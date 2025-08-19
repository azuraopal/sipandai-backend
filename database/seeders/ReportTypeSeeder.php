<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $reportTypes = [
            ['name' => 'Laporan Keuangan Bulanan', 'description' => 'Laporan detail mengenai pemasukan dan pengeluaran bulanan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Tahunan', 'description' => 'Laporan ringkasan kinerja selama satu tahun penuh.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Penjualan', 'description' => 'Data hasil penjualan produk dan layanan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Inventaris', 'description' => 'Daftar aset dan barang yang dimiliki.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Proyek', 'description' => 'Ringkasan progress proyek yang sedang berjalan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Audit', 'description' => 'Laporan hasil audit internal maupun eksternal.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Rapat', 'description' => 'Dokumentasi hasil rapat dan keputusan penting.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Insiden', 'description' => 'Pencatatan kejadian atau insiden yang terjadi.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Pelatihan', 'description' => 'Dokumentasi kegiatan pelatihan karyawan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Evaluasi', 'description' => 'Penilaian dan evaluasi terhadap kinerja tertentu.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Produksi', 'description' => 'Data hasil produksi barang selama periode tertentu.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Distribusi', 'description' => 'Informasi distribusi barang ke berbagai wilayah.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Marketing', 'description' => 'Strategi pemasaran dan hasil kegiatan promosi.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Pelanggan', 'description' => 'Data mengenai kepuasan dan feedback pelanggan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Sumber Daya Manusia', 'description' => 'Informasi terkait karyawan dan manajemen SDM.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Anggaran', 'description' => 'Detail perencanaan dan realisasi anggaran.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Pajak', 'description' => 'Laporan kewajiban perpajakan perusahaan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Perjalanan Dinas', 'description' => 'Rangkuman perjalanan dinas pegawai.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Kesehatan dan Keselamatan', 'description' => 'Data terkait kesehatan dan keselamatan kerja.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Penelitian', 'description' => 'Hasil penelitian dan eksperimen tertentu.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Teknologi', 'description' => 'Perkembangan teknologi dan sistem informasi.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Risiko', 'description' => 'Identifikasi dan manajemen risiko perusahaan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Keamanan', 'description' => 'Catatan keamanan fisik maupun siber.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan CSR', 'description' => 'Tanggung jawab sosial perusahaan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Supplier', 'description' => 'Data supplier dan evaluasi kualitasnya.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Logistik', 'description' => 'Proses manajemen logistik perusahaan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Performa Produk', 'description' => 'Analisis performa produk di pasar.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Harian', 'description' => 'Catatan aktivitas harian perusahaan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Mingguan', 'description' => 'Ringkasan progress mingguan.', 'image_url' => 'https://via.placeholder.com/150'],
            ['name' => 'Laporan Khusus', 'description' => 'Laporan untuk kebutuhan khusus dan mendesak.', 'image_url' => 'https://via.placeholder.com/150'],
        ];

        foreach ($reportTypes as $report) {
            DB::table('report_types')->insert([
                'name' => $report['name'],
                'description' => $report['description'],
                'image_url' => $report['image_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}