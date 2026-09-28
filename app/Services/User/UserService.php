<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    public function paginate(
        ?string $search = null,
        int $page = 1
    ): LengthAwarePaginator {
        $search = trim($search ?? '');

        return User::query()
            ->select([
                'UserID',
                'FirstName',
                'LastName',
                'UserName',
                'Email',
                'mobile',
                'isActive',
            ])
            ->where('isDeleted', false)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $pattern = '%' . $search . '%';

                    $query
                        ->where('FirstName', 'like', $pattern)
                        ->orWhere('LastName', 'like', $pattern)
                        ->orWhere('UserName', 'like', $pattern)
                        ->orWhere('Email', 'like', $pattern)
                        ->orWhere('mobile', 'like', $pattern);
                });
            })
            ->orderByDesc('UserID')
            ->paginate(
                perPage: 15,
                page: $page
            );
    }

    public function create(
        array $data,
        int $createdBy,
        ?string $ipAddress
    ): User {
        $user = new User();

        $user->FirstName = $data['FirstName'];
        $user->LastName = $data['LastName'];
        $user->UserName = $data['UserName'];
        $user->Password = $data['Password'];

        $user->Email = $data['Email'] ?? null;
        $user->mobile = $data['mobile'] ?? null;
        $user->nationalCode = $data['nationalCode'] ?? null;

        $user->isActive = (bool) $data['isActive'];
        $user->RequireChangePassword = (bool) $data['RequireChangePassword'];
        $user->isDeleted = false;
        $user->attemptCount = 0;

        $user->LastUserIDRef = $createdBy;
        $user->userIP = $ipAddress;

        $user->save();

        return $user;
    }
}