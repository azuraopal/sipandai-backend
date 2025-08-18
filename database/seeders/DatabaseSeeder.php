<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DistrictSeeder::class,
            VillageSeeder::class,
            OpdSeeder::class,
            UserSeeder::class,
            ReportTypeSeeder::class,
            ReportCategorySeeder::class,
            ReportSeeder::class,
            ReportHistorySeeder::class,
            ReportAttachmentSeeder::class,  
    ]);
    }
}