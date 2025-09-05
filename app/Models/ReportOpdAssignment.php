<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportOpdAssignment extends Model
{
    use HasFactory;

    protected $table = 'report_opd_assignments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'report_id',
        'opd_id',
        'assigned_by',
        'assigned_at',
        'ended_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function opd()
    {
        return $this->belongsTo(User::class, 'opd_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
