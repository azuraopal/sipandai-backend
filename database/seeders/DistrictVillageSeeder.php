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

        foreach ($districts as $district) {
            $districtCode = substr($district['id'], -2);

            DB::table('districts')->updateOrInsert(
                ['code' => $districtCode],
                [
                    'name' => $district['name'],
                    'created_at' => now(),
                ]
            );

            $villages = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$district['id']}.json")->json();

            foreach ($villages as $village) {
                DB::table('villages')->updateOrInsert(
                    ['code' => substr($village['id'], -5)],
                    [
                        'district_code' => $districtCode,
                        'name' => $village['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}