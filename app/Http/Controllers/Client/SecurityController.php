<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Security\UpdatePasswordRequest;
use App\Http\Requests\Client\Security\RevokeSessionRequest;
use App\Repositories\Client\Interfaces\SecurityRepositoryInterface;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    protected SecurityRepositoryInterface $securityRepo;

    public function __construct(SecurityRepositoryInterface $securityRepo)
    {
        $this->securityRepo = $securityRepo;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $sessions = $this->securityRepo->getSessions($user->id);

        return view('client.pages.dashboard.security', compact('sessions'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $this->securityRepo->updatePassword($user, $request->new_password);

        $request->session()->regenerate();

        return back()->with('status', 'Password updated successfully.');
    }

    public function logoutAllSessions(Request $request)
    {
        $user = $request->user();
        $this->securityRepo->logoutAllSessions($user->id);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function revokeSession(RevokeSessionRequest $request)
    {
        $user = $request->user();
        $this->securityRepo->revokeSession($request->session_id, $user->id);

        return back()->with('status', 'Session revoked.');
    }
}
