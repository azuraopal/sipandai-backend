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
    public function run(): void
    {
        $users = [
            // Warga
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::CITIZEN->value,
                'profile_picture_url' => null,
                'district_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Petugas Lapangan
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => 'Siti Aminah',
                'email' => 'siti.field@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::FIELD_OFFICER->value,
                'profile_picture_url' => null,
                'district_id' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Petugas QC
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => 'Andi Pratama',
                'email' => 'andi.qc@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::QC_OFFICER->value,
                'profile_picture_url' => null,
                'district_id' => '20',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Admin OPD
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => DB::table('opds')->inRandomOrder()->value('id'),
                'full_name' => 'Ratna Dewi',
                'email' => 'ratna.opd@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::OPD_ADMIN->value,
                'profile_picture_url' => null,
                'district_id' => '21',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Admin Kecamatan
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => 'Joko Kurniawan',
                'email' => 'joko.kecamatan@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::DISTRICT_ADMIN->value,
                'profile_picture_url' => null,
                'district_id' => '22',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Admin PEMDA
            [
                'id' => Str::uuid(),
                'google_id' => null,
                'opd_id' => null,
                'full_name' => 'Ahmad Fauzi',
                'email' => 'ahmad.pemda@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => UserRole::CITY_ADMIN->value,
                'profile_picture_url' => null,
                'district_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}