<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'obs_table_company';

    protected $primaryKey = 'companyIdRef';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'shortTitle',
        'nickName',
        'parentCompanyIdRef',
        'gradeType',
        'branchId',
        'gradeId',
        'issueDate',
        'validDate',
        'companyTypeIdRef',
        'sort',
        'isActive',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'companyIdRef' => 'integer',
            'adminIdRef' => 'integer',
            'parentCompanyIdRef' => 'integer',
            'gradeType' => 'integer',
            'branchId' => 'integer',
            'gradeId' => 'integer',
            'issueDate' => 'date',
            'validDate' => 'date',
            'companyTypeIdRef' => 'integer',
            'sort' => 'integer',
            'isActive' => 'boolean',
            'type' => 'integer',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(
            Person::class,
            'companyIdRef',
            'PersonID'
        );
    }

    public function adminPosition(): BelongsTo
    {
        return $this->belongsTo(
            Obs::class,
            'adminIdRef',
            'obsId'
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parentCompanyIdRef',
            'companyIdRef'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parentCompanyIdRef',
            'companyIdRef'
        );
    }

    public function positions(): HasMany
    {
        return $this->hasMany(
            Obs::class,
            'companyIdRef',
            'companyIdRef'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isActive', 1);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parentCompanyIdRef');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('companyIdRef');
    }
}