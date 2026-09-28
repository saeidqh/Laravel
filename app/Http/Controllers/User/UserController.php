<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ListUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User\User;
use App\Services\Access\FormActionAccess;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    private const CREATE_ACTION_ID = 332;

    public function list(
        ListUserRequest $request,
        UserService $userService,
        FormActionAccess $access
    ): View|JsonResponse {
        $data = $request->validated();

        $search = trim($data['search'] ?? '');

        $users = $userService->paginate(
            search: $search,
            page: (int) ($data['page'] ?? 1)
        );

        // AJAX requests pass through the same action-300 middleware.
        if ($request->expectsJson()) {
            return response()->json($users);
        }

        $user = $request->user('web');

        abort_unless($user instanceof User, 401);

        // The middleware has already validated the active OBS assignment
        // for non-IPC administrators.
        $canCreate = $user->isIPCAdmin()
            || $access->can(
                (int) $user->getAuthIdentifier(),
                (int) $request->session()->get('active_obs_id'),
                self::CREATE_ACTION_ID
            );

        return view('user.list', [
            'pageData' => [
                'initialUsers' => $users->toArray(),
                'initialSearch' => $search,
                'listUrl' => route('users.list'),
                'createUrl' => route('users.create'),
                'dashboardUrl' => route('dashboard'),
                'canCreate' => $canCreate,
            ],
        ]);
    }

    public function create(): View
    {
        return view('user.create');
    }

    public function store(
        StoreUserRequest $request,
        UserService $userService
    ): JsonResponse {
        $user = $userService->create(
            $request->validated(),
            (int) $request->user('web')->getAuthIdentifier(),
            $request->ip()
        );

        return response()->json([
            'message' => 'User created successfully.',
            'user' => [
                'id' => $user->getKey(),
                'username' => $user->UserName,
            ],
            'redirect_url' => route('users.list'),
        ], 201);
    }
}