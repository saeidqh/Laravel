<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Services\Access\FormActionAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // مطابق توضیح middleware فهرست در UserController.
    private const LIST_USERS_ACTION_ID = 300;

    public function __invoke(
        Request $request,
        FormActionAccess $access
    ): View {
        $user = $request->user('web');

        abort_unless($user instanceof User, 401);

        $activeObsId = (int) $request->session()->get('active_obs_id');

        $canList = $user->isIPCAdmin();

        if (!$canList && $activeObsId > 0) {
            $canList = (bool) $access->can(
                (int) $user->getAuthIdentifier(),
                $activeObsId,
                self::LIST_USERS_ACTION_ID
            );
        }

        return view('dashboard', [
            'canList' => $canList,
        ]);
    }
}