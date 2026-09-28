<?php

namespace App\Models\Obs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ObsTitle extends Model
{
    protected $table = 'obs_table_obs_title';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'Title',
        'sort',
        'isActive',
    ];

    protected function casts(): array
    {
        return [
            'ID' => 'integer',
            'relatedObsIdRef' => 'integer',
            'DefaultRole' => 'integer',
            'sort' => 'integer',
            'isActive' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isActive', 1);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('ID');
    }
}