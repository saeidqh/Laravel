<?php

namespace App\Models\User;

use App\Models\Obs\Obs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserObs extends Model
{
    protected $table = 'user_table_user_obs';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'ID' => 'integer',
            'UserIDRef' => 'integer',
            'OBSCategory3IDRef' => 'integer',
            'isActive' => 'boolean',
            'isDeleted' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'UserIDRef',
            'UserID'
        );
    }

    public function obs(): BelongsTo
    {
        return $this->belongsTo(
            Obs::class,
            'OBSCategory3IDRef',
            'obsId'
        );
    }
}