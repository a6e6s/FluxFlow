<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Tasks\ResolvePendingInvitations;
use App\Models\TaskInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function show(string $token, ResolvePendingInvitations $resolver): RedirectResponse
    {
        $invitation = TaskInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->isExpired()) {
            return redirect()->route('login')
                ->with('status', __('invitations.expired'));
        }

        if (! Auth::check()) {
            session(['invitation_token' => $token]);

            return redirect()->route('register', ['email' => $invitation->email]);
        }

        $user = Auth::user();

        if (mb_strtolower((string) $user->email) === mb_strtolower($invitation->email)) {
            $resolver->handle($user);

            return redirect()->route('dashboard')
                ->with('status', __('invitations.accepted'));
        }

        return redirect()->route('dashboard')
            ->with('status', __('invitations.email_mismatch'));
    }
}
