<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormAction extends Model
{
    protected $table = 'user_table_form_action';

    protected $primaryKey = 'ActionID';

    protected $fillable = [
        'ActionTitle',
        'Description',
        'actionType',
        'module',
        'ActionStatus',
    ];

    protected $casts = [
        'ActionStatus' => 'boolean',
    ];

    public function obsPermissions(): HasMany
    {
        return $this->hasMany(
            ObsFormAction::class,
            'FormActionIDRef',
            'ActionID'
        );
    }

    public function userObsPermissions(): HasMany
    {
        return $this->hasMany(
            UserObsFormAction::class,
            'FormActionIDRef',
            'ActionID'
        );
    }
}