<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class ReportHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

        $histories = [];

        // ambil semua report yg sudah ada
        $reports = DB::table('reports')->pluck('id');

        foreach ($reports as $reportId) {
            // setiap report punya 3-5 history status random
            $count = rand(3, 5);
            $pickedStatuses = collect($statuses)->random($count)->values();

            foreach ($pickedStatuses as $status) {
                $histories[] = [
                    'id' => Str::uuid(),
                    'report_id' => $reportId,
                    'user_id' => DB::table('users')->inRandomOrder()->value('id'),
                    'status' => $status,
                    'notes' => "Perubahan status ke $status untuk report $reportId",
                    'created_at' => now()->subDays(rand(0, 30)),
                ];
            }
        }

        DB::table('report_status_histories')->insert($histories);
    }
}