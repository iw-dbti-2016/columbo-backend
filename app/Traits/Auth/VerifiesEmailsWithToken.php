<?php

namespace Columbo\Traits\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Columbo\User;

trait VerifiesEmailsWithToken
{
    use RedirectsUsers;

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        // if ($request->route('id') != $request->user()->getKey()) {
        //     throw new AuthorizationException;
        // }

        $user = User::find($request->route('id'));

        if (! $user) {
            return $this->failedVerificationResponse();
        }

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedResponse();
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->correctResponse();
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->alreadyVerifiedResponse();
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->OKResendResponse();
    }

    protected function alreadyVerifiedResponse()
    {
        return $this->correctResponse();
    }

    protected function correctResponse()
    {
        return redirect($this->redirectPath())->with('verified', true);
    }

    protected function OKResendResponse()
    {
        return back()->with('resent', true);
    }

    protected function failedVerificationResponse()
    {
        return abort(403, "This link is invalid");
    }
}
