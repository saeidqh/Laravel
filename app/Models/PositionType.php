<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PositionType extends Model
{
    protected $table = 'automation_table_position_type';

    protected $primaryKey = 'PositionTypeID';

   

    public $timestamps = false;

    protected $fillable = [
        'Title',
        'IsManagerial',
        'isActive',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'PositionTypeID' => 'integer',
            'IsManagerial' => 'boolean',
            'isActive' => 'boolean',
            'sort' => 'integer',
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
            ->orderBy('PositionTypeID');
    }
}