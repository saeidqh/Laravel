<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    private const IPC_ADMIN_OBS_ID = 1;

    protected $table = 'user_table_user';

    protected $primaryKey = 'UserID';

    public $timestamps = false;

    protected $rememberTokenName = '';

    protected $hidden = [
        'Password',
        'loginToken',
    ];

    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
            'isActive' => 'boolean',
            'isDeleted' => 'boolean',
            'RequireChangePassword' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'Password';
    }

    public function userObs(): HasMany
    {
        return $this->hasMany(
            UserObs::class,
            'UserIDRef',
            'UserID'
        );
    }

    public function activeUserObs(): HasMany
    {
        return $this->userObs()
            ->where('user_table_user_obs.isActive', 1)
            ->where('user_table_user_obs.isDeleted', 0);
    }

    public function isIPCAdmin(): bool
    {
        if (! $this->exists || ! $this->isActive || $this->isDeleted) {
            return false;
        }

        return $this->activeUserObs()
            ->whereHas('obs', function (Builder $query): void {
                $query
                    ->whereKey(self::IPC_ADMIN_OBS_ID)
                    ->active();
            })
            ->exists();
    }
}