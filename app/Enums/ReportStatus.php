<?php

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING_VERIFICATION = 'PENDING_VERIFICATION';
    case NEEDS_REVIEW = 'NEEDS_REVIEW';
    case APPROVED = 'APPROVED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case PENDING_QA_REVIEW = 'PENDING_QA_REVIEW';
    case NEEDS_REVISION = 'NEEDS_REVISION';
    case REJECTED = 'REJECTED';
    case COMPLETED = 'COMPLETED';

    public function label(): string {
        return match ($this) {
            self::PENDING_VERIFICATION => 'Sedang Diverifikasi',
            self::NEEDS_REVIEW => 'Membutuhkan Verifikasi Lanjutan',
            self::APPROVED => 'Disetujui & Dalam Penugasan',
            self::IN_PROGRESS => 'Sedang Ditangani di Lapangan',
            self::PENDING_QA_REVIEW => 'Pemeriksaan Hasil Pekerjaan',
            self::NEEDS_REVISION => 'Membutuhkan Perbaikan',
            self::REJECTED => 'Ditolak',
            self::COMPLETED => 'Selesai',
        };
    }
}
