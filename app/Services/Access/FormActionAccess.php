<?php

namespace App\Services\Access;

use App\Models\Access\FormAction;
use App\Models\Access\ObsFormAction;
use App\Models\Access\UserObsFormAction;

class FormActionAccess
{
    /**
     * Check action permission within a verified user/OBS context.
     *
     * The caller must verify that the user is allowed to use this OBS.
     * Super-admin bypass and company scope are handled separately.
     */
    public function can(
        int $userId,
        int $obsId,
        int $actionId
    ): bool {
        if ($userId <= 0 || $obsId <= 0 || $actionId <= 0) {
            return false;
        }

        if (! $this->isActionActive($actionId)) {
            return false;
        }

        $userPermission = $this->getUserPermission(
            $userId,
            $obsId,
            $actionId
        );

        if ($userPermission !== null) {
            return $userPermission;
        }

        return $this->hasObsPermission($obsId, $actionId);
    }

    private function isActionActive(int $actionId): bool
    {
        return FormAction::query()
            ->whereKey($actionId)
            ->where('ActionStatus', true)
            ->exists();
    }

    /**
     * Null means no active user override exists.
     * False means access is explicitly denied.
     */
    private function getUserPermission(
        int $userId,
        int $obsId,
        int $actionId
    ): ?bool {
        $permission = UserObsFormAction::query()
            ->where('UserIDRef', $userId)
            ->where('OBSCategory3IDRef', $obsId)
            ->where('FormActionIDRef', $actionId)
            ->where('isDeleted', false)
            ->first(['AccessValue']);

        if ($permission === null) {
            return null;
        }

        return $permission->AccessValue;
    }

    private function hasObsPermission(
        int $obsId,
        int $actionId
    ): bool {
        return ObsFormAction::query()
            ->where('OBSCategory3IDRef', $obsId)
            ->where('FormActionIDRef', $actionId)
            ->where('AccessValue', true)
            ->where('isDeleted', false)
            ->exists();
    }
}