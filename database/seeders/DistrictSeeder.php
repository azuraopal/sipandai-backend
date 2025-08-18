<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('districts')->truncate();

        $districts = [
            ['code' => '01', 'name' => 'Kecamatan Bandung Wetan', 'created_at' => now()],
            ['code' => '02', 'name' => 'Kecamatan Cibeunying Kidul', 'created_at' => now()],
            ['code' => '03', 'name' => 'Kecamatan Sukasari', 'created_at' => now()],
            ['code' => '04', 'name' => 'Kecamatan Gedebage', 'created_at' => now()],
            ['code' => '05', 'name' => 'Kecamatan Andir', 'created_at' => now()],
            ['code' => '06', 'name' => 'Kecamatan Babakan Ciparay', 'created_at' => now()],
            ['code' => '07', 'name' => 'Kecamatan Cicadas', 'created_at' => now()],
            ['code' => '08', 'name' => 'Kecamatan Batununggal', 'created_at' => now()],
            ['code' => '09', 'name' => 'Kecamatan Kopo', 'created_at' => now()],
            ['code' => '10', 'name' => 'Kecamatan Lengkong', 'created_at' => now()],
        ];

        DB::table('districts')->insert($districts);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}