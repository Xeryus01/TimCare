<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, AuditTrailService $auditTrail): RedirectResponse
    {
        $request->authenticate();
        $auditTrail->record($request, $request->user()->id, 'USER', $request->user()->id, 'LOGIN');

        // Ensure session is saved before redirect
        $request->session()->regenerate();
        $request->session()->save();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, AuditTrailService $auditTrail): RedirectResponse
    {
        $userId = $request->user()->id;
        $auditTrail->record($request, $userId, 'USER', $userId, 'LOGOUT');
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(url('/'));
    }
}
