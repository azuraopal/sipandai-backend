<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'name',
    ];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class, 'type_id');
    }
}
