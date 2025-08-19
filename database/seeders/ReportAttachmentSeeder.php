<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class ReportAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $purposes = [
            'Dokumentasi Lapangan',
            'Bukti Nota',
            'Foto Kegiatan',
            'Surat Resmi',
            'Laporan Tambahan',
            'Dokumen Pendukung',
            'Lampiran Hasil Survey',
            'RAB Pendukung',
        ];

        $fileTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'application/msword',
            'application/vnd.ms-excel'
        ];

        $attachments = [];

        for ($i = 1; $i <= 30; $i++) {
            $attachments[] = [
                'id' => Str::uuid(),
                'report_id' => DB::table('reports')->inRandomOrder()->value('id'),
                'purpose' => $purposes[array_rand($purposes)],
                'file_url' => 'https://example.com/files/report_attachment_' . $i . '.' . (rand(0, 1) ? 'pdf' : 'jpg'),
                'file_type' => $fileTypes[array_rand($fileTypes)],
                'created_at' => now(),
            ];
        }

        DB::table('report_attachments')->insert($attachments);
    }
}