<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Policies\OpdPolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

#[OpdPolicy(Opd::class)]
class Opd extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'opds';

    protected $fillable = [
        'name'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class, 'opd_id', 'id');
    }
}