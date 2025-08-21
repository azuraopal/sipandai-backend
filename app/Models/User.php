<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'profile_picture_url',
        'district_id',
        'opd_id',
        'google_id',
        'role',
    ];

    /**
     * Hide secrets from API responses.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code_hash',
        'verification_code_expires_at',
        'google_id',
    ];

    /**
     * Append computed "role", "role_label" to keep API shape consistent.
     */
    protected $appends = ['role_label'];

    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    /**
     * Label derived from the (casted) enum.
     */
    public function getRoleLabelAttribute(): string
    {
        $role = $this->getAttribute('role');
        if ($role instanceof UserRole) {
            return $role->label();
        }

        return $role ? (string) $role : '';
    }

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Always store email lowercased & trimmed.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => mb_strtolower(trim($value))
        );
    }

    public function assignedTask() 
    {
        return $this->hasMany(ReportAssignment::class, 'assigned_to_user_id');
    }

    public function givenTask()
    {
        return $this->hasMany(ReportAssignment::class, 'assigned_by_user_id');
    }
}
