<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportStatusHistory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'report_id',
        'user_id',
        'status',
        'note'
    ];

    protected $casts = [
        'status' => ReportStatus::class,
    ];

    protected function report(): BelongsTo 
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
