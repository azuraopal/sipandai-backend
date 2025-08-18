<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAttachment extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'report_id',
        'purpose',
        'file_url',
        'file_type',
    ];

    protected $casts = [
        'purpose' => AttachmentPurpose::class,
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
