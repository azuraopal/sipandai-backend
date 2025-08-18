<?php

namespace App\Enums;

enum UserRole: string
{
    case CITIZEN = 'CITIZEN';
    case FIELD_OFFICER = 'FIELD_OFFICER';
    case QC_OFFICER = 'QC_OFFICER';
    case OPD_ADMIN = 'OPD_ADMIN';
    case DISTRICT_ADMIN = 'DISTRICT_ADMIN'; 
    case CITY_ADMIN = 'CITY_ADMIN';

    public function label(): string {
        return match ($this) {
            self::CITIZEN => 'Warga',
            self::FIELD_OFFICER => 'Petugas Lapangan',
            self::QC_OFFICER => 'Petugas Quality Control',
            self::OPD_ADMIN => 'Admin OPD',
            self::DISTRICT_ADMIN => 'Admin Kecamatan',
            self::CITY_ADMIN => 'Admin PEMDA',
        };
    }
}
