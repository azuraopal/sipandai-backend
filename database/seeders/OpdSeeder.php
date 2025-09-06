<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            // Sekretariat
            'Sekretariat Daerah Provinsi Jawa Barat',
            'Sekretariat DPRD Provinsi Jawa Barat',
            
            // Inspektorat
            'Inspektorat Daerah Provinsi Jawa Barat',
            
            // Badan
            'Badan Kepegawaian Daerah Provinsi Jawa Barat',
            'Badan Keuangan Daerah Provinsi Jawa Barat',
            'Badan Pendapatan Daerah Provinsi Jawa Barat',
            'Badan Penelitian dan Pengembangan Daerah Provinsi Jawa Barat',
            'Badan Perencanaan Pembangunan Daerah Provinsi Jawa Barat',
            'Badan Kesatuan Bangsa dan Politik Provinsi Jawa Barat',
            'Badan Penanggulangan Bencana Daerah Provinsi Jawa Barat',
            'Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Jawa Barat',

            // Dinas
            'Dinas Pendidikan Provinsi Jawa Barat',
            'Dinas Kesehatan Provinsi Jawa Barat',
            'Dinas Sosial Provinsi Jawa Barat',
            'Dinas Tenaga Kerja dan Transmigrasi Provinsi Jawa Barat',
            'Dinas Pekerjaan Umum dan Penataan Ruang Provinsi Jawa Barat',
            'Dinas Perhubungan Provinsi Jawa Barat',
            'Dinas Komunikasi dan Informatika Provinsi Jawa Barat',
            'Dinas Kependudukan dan Pencatatan Sipil Provinsi Jawa Barat',
            'Dinas Lingkungan Hidup Provinsi Jawa Barat',
            'Dinas Perindustrian dan Perdagangan Provinsi Jawa Barat',
            'Dinas Koperasi dan Usaha Kecil Provinsi Jawa Barat',
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Provinsi Jawa Barat',
            'Dinas Pemuda dan Olahraga Provinsi Jawa Barat',
            'Dinas Kebudayaan dan Pariwisata Provinsi Jawa Barat',
            'Dinas Perpustakaan dan Kearsipan Daerah Provinsi Jawa Barat',
            'Dinas Perikanan dan Kelautan Provinsi Jawa Barat',
            'Dinas Tanaman Pangan dan Hortikultura Provinsi Jawa Barat',
            'Dinas Perkebunan Provinsi Jawa Barat',
            'Dinas Peternakan Provinsi Jawa Barat',
            'Dinas Ketahanan Pangan Provinsi Jawa Barat',
            'Dinas Energi dan Sumber Daya Mineral Provinsi Jawa Barat',
            'Dinas Kehutanan Provinsi Jawa Barat',
            'Dinas Perumahan dan Kawasan Permukiman Provinsi Jawa Barat',
            'Dinas Pemberdayaan Masyarakat dan Desa Provinsi Jawa Barat',
            'Dinas Perlindungan Anak dan Pemberdayaan Perempuan Provinsi Jawa Barat',
            'Dinas Arsip dan Perpustakaan Provinsi Jawa Barat',
            'Dinas Satpol PP Provinsi Jawa Barat',

            // Rumah Sakit Umum Daerah
            'RSUD Al Ihsan Provinsi Jawa Barat',
            'RS Jiwa Provinsi Jawa Barat',
            'RS Paru Dr. M. Goenawan Partowidigdo Cisarua',
        ];

        foreach ($opds as $name) {
            DB::table('opds')->insert([
                'id' => (string) Str::uuid(),
                'name' => $name,
                'created_at' => now(),
            ]);
        }
    }
}