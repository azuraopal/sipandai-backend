<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('villages')->truncate();

        $villages = [];
        $districtCodes = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];

        foreach ($districtCodes as $districtCode) {
            for ($i = 1; $i <= 5; $i++) {
                $villages[] = [
                    'code' => $districtCode . Str::padLeft($i, 3, '0'),
                    'district_code' => $districtCode,
                    'name' => 'Desa ' . Str::random(5) . ' ' . $districtCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('villages')->insert($villages);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}