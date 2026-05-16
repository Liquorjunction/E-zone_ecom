<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Security\UpdatePasswordRequest;
use App\Http\Requests\Client\Security\RevokeSessionRequest;
use App\Repositories\Client\Interfaces\SecurityRepositoryInterface as SecurityRepo;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index(Request $request, SecurityRepo $securityRepo)
    {
        $user = $request->user();
        $sessions = $securityRepo->getSessions($user->id);
        $lastPasswordUpdate = $securityRepo->getLastPasswordUpdateTime($user->id);

        return view('client.pages.dashboard.security', compact('sessions', 'lastPasswordUpdate'));
    }

    public function sessionsCount(Request $request, SecurityRepo $securityRepo)
    {
        $user = $request->user();

        return response()->json([
            'count' => $securityRepo->countSessions($user->id),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request, SecurityRepo $securityRepo)
    {
        $user = $request->user();

        if (! $securityRepo->validateCurrentPassword($user, $request->current_password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'errors' => ['current_password' => [statictranslations('The current password is incorrect.')]]
                ], 422);
            }
            return back()->withErrors(['current_password' => statictranslations('The current password is incorrect.')])->withInput();
        }

        $securityRepo->updatePassword($user, $request->new_password);
        $request->session()->regenerate();
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => statictranslations('Password updated successfully.')]);
        }
        return back()->with('success', statictranslations('Password updated successfully.'));
    }

    public function logoutAllSessions(Request $request, SecurityRepo $securityRepo)
    {
        $user = $request->user();
        $securityRepo->logoutAllSessions($user->id);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function revokeSession(RevokeSessionRequest $request, SecurityRepo $securityRepo)
    {
        $user = $request->user();
        $securityRepo->revokeSession($request->session_id, $user->id);

        return back()->with('status', statictranslations('Session revoked.'));
    }
}
