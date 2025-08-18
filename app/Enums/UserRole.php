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
}
