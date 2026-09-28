<?php

namespace App\Models\Obs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ObsCategory extends Model
{
    protected $table = 'obs_table_category';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }
}