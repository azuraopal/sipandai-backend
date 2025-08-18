<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class PersonalAccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $tokens = [];

        for ($i = 1; $i <= 10; $i++) {
            $tokens[] = [
                'tokenable_type' => 'App\\Models\\User', // biasanya personal_access_tokens dipakai user
                'tokenable_id'   => $i, // sesuaikan dengan id user yang ada
                'name'           => 'API Token User ' . $i,
                'token'          => hash('sha256', Str::random(40)), // default Laravel generate token
                'abilities'      => json_encode(['*']), // full access
                'last_used_at'   => $now->subDays(rand(0, 30)),
                'expires_at'     => $now->addDays(rand(30, 90)),
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('personal_access_tokens')->insert($tokens);
    }
}