<?php

namespace App\Models;

use App\Policies\DistrictPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[DistrictPolicy(District::class)]
class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'districts';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name'
    ];

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    // protected $with = ['villages'];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function users()
    {
        return $this->hasMany(User::class, 'district_id', 'code');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'district_code', 'code');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'district_id', 'code');
    }
}