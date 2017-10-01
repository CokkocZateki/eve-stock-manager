<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the EVE Online SSO page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('eveonline')->redirect();
    }

    /**
     * Obtain the user information from EVE Online, check that the user is a member of the correct corporation.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('eveonline')->user();

        dd($user);
    }
}
