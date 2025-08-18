<?php

namespace App\Enums;

enum AttachmentPurpose: string
{
    case INITIAL_REPORT = 'INITIAL_REPORT';
    case EVIDENCE = 'EVIDENCE';
    case COMPLETION = 'COMPLETION';
}
