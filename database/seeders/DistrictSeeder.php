<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Menonaktifkan pemeriksaan foreign key sementara untuk memastikan tabel bisa dikosongkan.
        // Ini tidak diperlukan untuk tabel ini, tetapi adalah praktik yang baik.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Mengosongkan tabel 'districts' sebelum memasukkan data baru
        DB::table('districts')->truncate();

        // Data dummy untuk tabel 'districts'
        $districts = [
            ['code' => '01', 'name' => 'Kecamatan Bandung Wetan'],
            ['code' => '02', 'name' => 'Kecamatan Cibeunying Kidul'],
            ['code' => '03', 'name' => 'Kecamatan Sukasari'],
            ['code' => '04', 'name' => 'Kecamatan Gedebage'],
            ['code' => '05', 'name' => 'Kecamatan Andir'],
            ['code' => '06', 'name' => 'Kecamatan Babakan Ciparay'],
            ['code' => '07', 'name' => 'Kecamatan Cicadas'],
            ['code' => '08', 'name' => 'Kecamatan Batununggal'],
            ['code' => '09', 'name' => 'Kecamatan Kopo'],
            ['code' => '10', 'name' => 'Kecamatan Lengkong'],
        ];

        // Memasukkan data ke dalam tabel
        DB::table('districts')->insert($districts);
        
        // Mengaktifkan kembali pemeriksaan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}