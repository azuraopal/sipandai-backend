<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $with = [
        'user',
        'attachments',
    ];

    protected $fillable = [
        'report_code',
        'user_id',
        'type_id',
        'category_id',
        'title',
        'description',
        'district_id',
        'village_id',
        'address_detail',
        'phone_number',
        'coordinates',
        'current_opd_id',
        'current_officer_id',
        'current_status',
    ];

    protected $casts = [
        'current_status' => ReportStatus::class,
        'purpose' => AttachmentPurpose::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select([
            'id',
            'full_name',
            'email',
            'profile_picture_url'
        ]);
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class, 'type_id');
    }

    public function reportCategory(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'code');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ReportUserAssignment::class);
    }

    /**
     * @param  mixed  $value
     * @return array|null
     */
    public function getCoordinatesAttribute($value): ?array
    {
        $result = DB::select("SELECT ST_Y(coordinates) as latitude, ST_X(coordinates) as longitude FROM reports WHERE id = ?", [$this->id]);

        if (empty($result)) {
            return null;
        }

        return [
            'latitude' => $result[0]->latitude,
            'longitude' => $result[0]->longitude,
        ];
    }
}