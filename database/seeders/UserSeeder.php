<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Faker\Factory;
use Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $faker = Factory::create('id_ID');

        DB::table('users')->truncate();

        DB::table('users')->insert([
            'id' => (string) Str::uuid(),
            'google_id' => null,
            'opd_id' => null,
            'full_name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CITY_ADMIN->value,
            'profile_picture_url' => $faker->imageUrl(640, 480, 'people', true, 'Faker'),
            'district_id' => $faker->numberBetween(10, 99),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 50; $i++) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => UserRole::CITIZEN->value,
                'profile_picture_url' => $faker->imageUrl(640, 480, 'people', true, 'Faker'),
                'district_id' => $faker->numberBetween(10, 99),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'google_id' => null,
                'opd_id' => (string) Str::uuid(),
                'full_name' => $faker->name,
                'email' => 'opdstaff'.($i + 1).'@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::OPD_ADMIN->value,
                'profile_picture_url' => $faker->imageUrl(640, 480, 'business', true, 'Faker'),
                'district_id' => $faker->numberBetween(10, 99),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => $faker->name,
                'email' => 'districta'.($i + 1).'@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::DISTRICT_ADMIN->value,
                'profile_picture_url' => $faker->imageUrl(640, 480, 'business', true, 'Faker'),
                'district_id' => $faker->numberBetween(10, 99),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => $faker->name,
                'email' => 'qcofficer'.($i + 1).'@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::QC_OFFICER->value,
                'profile_picture_url' => $faker->imageUrl(640, 480, 'business', true, 'Faker'),
                'district_id' => $faker->numberBetween(10, 99),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => $faker->name,
                'email' => 'fieldofficer'.($i + 1).'@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::FIELD_OFFICER->value,
                'profile_picture_url' => $faker->imageUrl(640, 480, 'business', true, 'Faker'),
                'district_id' => $faker->numberBetween(10, 99),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
