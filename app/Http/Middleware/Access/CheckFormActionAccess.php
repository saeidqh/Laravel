<?php

namespace App\Http\Middleware\Access;

use App\Models\User\User;
use App\Services\Access\FormActionAccess;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFormActionAccess
{
    public function __construct(
        private readonly FormActionAccess $access
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $actionId,
        string $actionTitle = 'انجام این عملیات'
    ): Response {
        $user = $request->user('web');

        if (! $user instanceof User) {
            abort(Response::HTTP_UNAUTHORIZED, 'ابتدا وارد سامانه شوید.');
        }

        if (! $user->isActive || $user->isDeleted) {
            abort(
                Response::HTTP_FORBIDDEN,
                'حساب کاربری شما غیرفعال است.'
            );
        }

        // IPC Admin bypasses OBS selection and action permissions.
        if ($user->isIPCAdmin()) {
            return $next($request);
        }

        $obsId = $request->session()->get('active_obs_id');

        if (
            (! is_int($obsId) && ! is_string($obsId))
            || ! ctype_digit((string) $obsId)
            || (int) $obsId <= 0
        ) {
            abort(
                Response::HTTP_FORBIDDEN,
                'پست سازمانی جاری مشخص نشده است.'
            );
        }

        $obsId = (int) $obsId;

        $hasActiveAssignment = $user->activeUserObs()
            ->whereHas('obs', function (Builder $query) use ($obsId): void {
                $query->whereKey($obsId)->active();
            })
            ->exists();

        if (! $hasActiveAssignment) {
            abort(
                Response::HTTP_FORBIDDEN,
                'پست سازمانی انتخاب‌شده فعال نیست یا به شما اختصاص ندارد.'
            );
        }

        if (
            ! ctype_digit($actionId)
            || ! $this->access->can(
                (int) $user->getAuthIdentifier(),
                $obsId,
                (int) $actionId
            )
        ) {
            abort(
                Response::HTTP_FORBIDDEN,
                "شما مجوز «{$actionTitle}» را ندارید."
            );
        }

        return $next($request);
    }
}