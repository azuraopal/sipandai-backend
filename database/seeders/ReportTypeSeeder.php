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
            [
                'name' => 'Infrastruktur & Fasilitas Umum',
                'description' => 'Laporan terkait jalan, jembatan, penerangan jalan, gedung pemerintah, dan fasilitas umum lainnya.',
                'image_url' => 'https://via.placeholder.com/150?text=Infrastruktur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kebersihan dan Lingkungan',
                'description' => 'Laporan mengenai sampah menumpuk, saluran air tersumbat, banjir, serta kondisi lingkungan sekitar.',
                'image_url' => 'https://via.placeholder.com/150?text=Kebersihan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Keamanan & Ketertiban',
                'description' => 'Laporan terkait gangguan keamanan, kriminalitas, pelanggaran ketertiban umum, dan masalah serupa.',
                'image_url' => 'https://via.placeholder.com/150?text=Keamanan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('report_types')->insert($reportTypes);
    }
}