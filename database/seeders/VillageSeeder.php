<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('villages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $villages = [];
        $districtCodes = District::pluck('code')->unique()->toArray();

        foreach ($districtCodes as $districtCode) {
            for ($i = 1; $i <= 5; $i++) {
                $subCode = str_pad($i, 2, '0', STR_PAD_LEFT);
                $villageCode = $districtCode . '.' . $subCode;

                $villages[] = [
                    'code' => $villageCode,
                    'district_code' => $districtCode,
                    'name' => 'Kelurahan ' . Str::ucfirst(Str::lower(Str::random(6))),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($villages, 500) as $chunk) {
            DB::table('villages')->insert($chunk);
        }
    }
}