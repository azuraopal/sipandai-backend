<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_id',
        'name',
    ];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class, 'type_id');
    }
}
