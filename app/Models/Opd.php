<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Opd extends Model
{
    use HasFactory;

    protected $table = 'opds';
    
    protected $fillable = [
        'name'
    ];
    
    public $incrementing = false;
    protected $keyType = 'string';

    public function users() {
        return $this->hasMany(User::class, 'opd_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}