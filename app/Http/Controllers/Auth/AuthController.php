<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    /**
     * Redirect the user to the EVE Online SSO page and ask for permission to search corporation assets.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('eveonline')->scopes(['esi-assets.read_corporation_assets.v1'])->redirect();
    }

    /**
     * Obtain the user information from EVE Online, check that the user is a member of the correct corporation.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request)
    {
        $user = Socialite::driver('eveonline')->user();

        // Retrieve the character's corporation.
        $api_instance = new \nullx27\ESI\Api\CharacterApi();
        $characterId = $user->getId();
        
        try {
            $result = $api_instance->getCharactersCharacterId($characterId);
            $api_instance = new \nullx27\ESI\Api\CorporationApi();
            $corporationId = $result['corporationId'];
            try {
                $result = $api_instance->getCorporationsCorporationId($corporationId);
            } catch (Exception $e) {
                echo 'Exception when calling CorporationApi->getCorporationsCorporationId: ', $e->getMessage(), PHP_EOL;
            }
        } catch (Exception $e) {
            echo 'Exception when calling CharacterApi->getCharactersCharacterId: ', $e->getMessage(), PHP_EOL;
        }

        if ($result['corporationName'] == env('EVEONLINE_CORP_NAME', false))
        {
            $authUser = $this->findOrCreateUser($user);
            Auth::login($authUser, true);
        }

        return redirect('/');
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $user
     * @return User
     */
     private function findOrCreateUser($user)
     {
         if ($authUser = User::where('eve_id', $user->id)->first()) {
             $authUser->token = $user->token;
             $authUser->save();
             return $authUser;
         }
 
         return User::create([
             'eve_id' => $user->id,
             'name' => $user->name,
             'avatar' => $user->avatar,
             'token' => $user->token,
         ]);
     }
    
}
