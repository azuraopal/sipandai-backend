<?php

namespace App\Enums;

enum AttachmentPurpose: string
{
    case INITIAL_EVIDENCE = 'INITIAL_EVIDENCE';
    case COMPLETION_PROFF = 'COMPLETION_PROFF';

    public function label(): string {
        return match ($this) {
            self::INITIAL_EVIDENCE => 'Bukti Awal',
            self::COMPLETION_PROFF => 'Bukti Selesai',
        };
    }
}
