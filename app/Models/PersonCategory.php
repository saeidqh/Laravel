<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonCategory extends Model
{
    protected $table = 'obs_table_person_category';

    public $timestamps = false;

    protected $fillable = [
        'title',
    ];
}