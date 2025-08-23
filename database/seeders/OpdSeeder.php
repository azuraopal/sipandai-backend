<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpdSeeder extends Seeder
{
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('opds')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $opds = [
            'Dinas Pendidikan',
            'Dinas Kesehatan',
            'Dinas Perhubungan',
            'Dinas Pekerjaan Umum',
            'Dinas Sosial',
            'Dinas Kependudukan dan Catatan Sipil',
            'Dinas Pemuda dan Olahraga',
            'Dinas Lingkungan Hidup',
            'Dinas Pariwisata',
            'Dinas Pertanian',
            'Dinas Perikanan',
            'Dinas Perdagangan',
            'Dinas Koperasi dan UMKM',
            'Dinas Komunikasi dan Informatika',
            'Dinas Tenaga Kerja',
            'Dinas Perindustrian',
            'Dinas Kehutanan',
            'Dinas Perumahan dan Permukiman',
            'Dinas Pertanahan',
            'Dinas Energi dan Sumber Daya Mineral',
            'Dinas Arsip dan Perpustakaan',
            'Dinas Perpajakan',
            'Dinas Keuangan Daerah',
            'Dinas Bencana dan Kebakaran',
            'Dinas Transportasi Darat',
            'Dinas Transportasi Laut',
            'Dinas Transportasi Udara',
            'Dinas Penelitian dan Pengembangan',
            'Dinas Statistik Daerah',
            'Dinas Investasi dan Penanaman Modal',
        ];

        foreach ($opds as $opdName) {
            Opd::create([
                'name' => $opdName,
            ]);
        }
    }
}