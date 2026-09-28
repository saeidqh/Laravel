<?php

namespace App\Models\Obs;

use App\Models\PositionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obs extends Model
{
    protected $table = 'obs_table_obs_new';

    protected $primaryKey = 'obsId';

    public $timestamps = false;

    protected $fillable = [
        'obsTitleIdRef',
        'secondPartTitle',
        'parentObsIdRef',
        'categoryIdRef',
        'positionTypeIdRef',
        'positionCount',
        'notApprovalPositionCount',
        'status',
        'isActive',
    ];

    protected function casts(): array
    {
        return [
            'obsId' => 'integer',
            'obsTitleIdRef' => 'integer',
            'companyIdRef' => 'integer',
            'parentObsIdRef' => 'integer',
            'relatedObsIdRef' => 'integer',
            'categoryIdRef' => 'integer',
            'positionTypeIdRef' => 'integer',
            'positionCount' => 'integer',
            'notApprovalPositionCount' => 'integer',
            'isActive' => 'boolean',
            'verified' => 'boolean',
            'LastUserIDRef' => 'integer',
            'DefaultRole' => 'integer',
            'ProjectIDRef' => 'integer',
            'SiteIDRef' => 'integer',
            'processCodeIdRef' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parentObsIdRef', 'obsId');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parentObsIdRef', 'obsId');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(ObsTitle::class, 'obsTitleIdRef', 'ID');
    }

    public function positionType(): BelongsTo
    {
        return $this->belongsTo(
            PositionType::class,
            'positionTypeIdRef',
            'PositionTypeID'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ObsCategory::class, 'categoryIdRef', 'Id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isActive', 1);
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('companyIdRef', $companyId);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parentObsIdRef');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('obsId');
    }
}