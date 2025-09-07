<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DistrictVillageSeeder extends Seeder
{
    public function run(): void
    {
        $districts = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/3201.json")->json();

        $districtCounter = 1;
        foreach ($districts as $district) {
            $districtCode = str_pad($districtCounter, 2, '0', STR_PAD_LEFT);

            DB::table('districts')->updateOrInsert(
                ['code' => $districtCode],
                [
                    'name' => $district['name'],
                    'created_at' => now(),
                ]
            );

            $villages = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$district['id']}.json")->json();

            foreach ($villages as $village) {
                $villageCode = substr($village['id'], -2);
                $formattedCode = sprintf("%02d.%02d", $districtCode, $villageCode);

                DB::table('villages')->updateOrInsert(
                    ['code' => $formattedCode],
                    [
                        'district_code' => $districtCode,
                        'name' => $village['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            $districtCounter++;
        }
    }
}