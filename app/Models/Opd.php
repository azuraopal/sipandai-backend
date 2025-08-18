<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    use HasFactory;

    protected $table = 'opds';
    protected $fillable = ['name'];

    public function users() {
        return $this->hasMany(User::class, 'opd_id', 'id');
    }

// opd
}