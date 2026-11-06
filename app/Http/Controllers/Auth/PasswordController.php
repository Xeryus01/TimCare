<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request, AuditTrailService $auditTrail): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        DB::transaction(function () use ($request, $validated, $auditTrail): void {
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            $auditTrail->record($request, $request->user()->id, 'USER', $request->user()->id, 'PASSWORD_UPDATED');
        });

        return back()->with('status', 'password-updated');
    }
}
