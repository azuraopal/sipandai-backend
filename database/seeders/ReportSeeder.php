<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ReportStatus::PENDING_VERIFICATION->value,
            ReportStatus::NEEDS_REVIEW->value,
            ReportStatus::APPROVED->value,
            ReportStatus::IN_PROGRESS->value,
            ReportStatus::PENDING_QA_REVIEW->value,
            ReportStatus::NEEDS_REVISION->value,
            ReportStatus::REJECTED->value,
            ReportStatus::COMPLETED->value,
        ];

        $faker = \Faker\Factory::create('id_ID');
        $reports = [];

        foreach ($statuses as $status) {
            for ($i = 1; $i <= 10; $i++) {
                $reports[] = [
                    'id' => Str::uuid(),
                    'report_code' => strtoupper(Str::random(8)),
                    'user_id' => DB::table('users')->inRandomOrder()->value('id'),
                    'type_id' => DB::table('report_types')->inRandomOrder()->value('id'),
                    'category_id' => DB::table('report_categories')->inRandomOrder()->value('id'),
                    'district_id' => DB::table('districts')->inRandomOrder()->value('code'),
                    'village_id' => DB::table('villages')->inRandomOrder()->value('code'),
                    'title' => "Laporan Dummy [$status] #$i",
                    'description' => "Ini adalah laporan dummy dengan status $status.",
                    'address_detail' => "Alamat detail laporan dummy $i untuk status $status.",
                    'coordinates' => DB::raw("ST_GeomFromText('POINT(" . $faker->latitude . " " . $faker->longitude . ")', 4326)"),
                    'phone_number' => '08' . rand(1000000000, 9999999999),
                    'current_status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('reports')->insert($reports);
    }
}