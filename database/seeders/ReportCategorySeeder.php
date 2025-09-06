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
            // Infrastruktur & Fasilitas Umum
            ['type_id' => 1, 'name' => 'Jalan rusak / berlubang'],
            ['type_id' => 1, 'name' => 'Trotoar atau jalur sepeda rusak'],
            ['type_id' => 1, 'name' => 'Penerangan jalan mati'],
            ['type_id' => 1, 'name' => 'Drainase / saluran air tersumbat'],
            ['type_id' => 1, 'name' => 'Jembatan / gorong-gorong rusak'],

            // Kebersihan dan Lingkungan
            ['type_id' => 2, 'name' => 'Sampah menumpuk / tidak terangkut'],
            ['type_id' => 2, 'name' => 'Pencemaran air / sungai'],
            ['type_id' => 2, 'name' => 'Pencemaran udara (asap, bau, debu)'],
            ['type_id' => 2, 'name' => 'Hewan liar mengganggu lingkungan'],
            ['type_id' => 2, 'name' => 'Taman kota rusak / terbengkalai'],

            // Keamanan & Ketertiban
            ['type_id' => 3, 'name' => 'Lampu lalu lintas mati / rusak'],
            ['type_id' => 3, 'name' => 'Kecelakaan lalu lintas'],
            ['type_id' => 3, 'name' => 'Pelanggaran parkir / jalan'],
            ['type_id' => 3, 'name' => 'Gangguan keamanan'],
            ['type_id' => 3, 'name' => 'Kebakaran / potensi kebakaran'],
        ];

        foreach ($categories as $category) {
            DB::table('report_categories')->insert([
                'type_id' => $category['type_id'],
                'name' => $category['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}