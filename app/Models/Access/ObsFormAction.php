<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObsFormAction extends Model
{
    protected $table = 'user_table_obs_form_action';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'OBSCategory3IDRef',
        'FormActionIDRef',
        'AccessValue',
        'isDeleted',
    ];

    protected $casts = [
        'OBSCategory3IDRef' => 'integer',
        'FormActionIDRef' => 'integer',
        'AccessValue' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    public function formAction(): BelongsTo
    {
        return $this->belongsTo(
            FormAction::class,
            'FormActionIDRef',
            'ActionID'
        );
    }
}