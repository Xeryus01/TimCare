<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditTrailService;
use App\Support\ActivityLogPresenter;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $activityLogs = $user->logs()
            ->latest('created_at')
            ->paginate(6, ['*'], 'activity_page');
        $lastLoginAt = $user->logs()
            ->whereIn('action', ['LOGIN', 'SSO_LOGIN'])
            ->latest('created_at')
            ->value('created_at');
        $lastLoginAt = $lastLoginAt ?: $user->last_login_at;

        return view('profile.edit', [
            'user' => $user,
            'activityLogs' => $activityLogs,
            'lastLoginAt' => $lastLoginAt ? Carbon::parse($lastLoginAt) : null,
            'activityLogPresenter' => app(ActivityLogPresenter::class),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, AuditTrailService $auditTrail): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $changedFields = array_keys($user->getDirty());
        DB::transaction(function () use ($user, $changedFields, $request, $auditTrail): void {
            $user->save();

            if ($changedFields !== []) {
                $auditTrail->record($request, $user->id, 'USER', $user->id, 'PROFILE_UPDATED', [
                    'changed_fields' => $changedFields,
                ]);
            }
        });

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}
