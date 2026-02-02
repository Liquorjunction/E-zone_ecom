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

        return view('client.pages.dashboard.security', compact('sessions'));
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

        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $securityRepo->updatePassword($user, $request->new_password);

        $request->session()->regenerate();

        return back()->with('status', 'Password updated successfully.');
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

        return back()->with('status', 'Session revoked.');
    }
}
