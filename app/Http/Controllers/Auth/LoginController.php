<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $data = $request->validated();

        $authenticated = Auth::guard('web')->attempt([
            'UserName' => $data['username'],
            'password' => $data['password'],
            'isActive' => 1,
            'isDeleted' => 0,
        ], false);

        if (! $authenticated) {
            $request->recordFailedAttempt();

            throw ValidationException::withMessages([
                'username' => 'The provided credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();

        $request->clearLoginAttempts();

        return redirect()->intended(route('dashboard'));
    }
public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}