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

        // Retrieve the character's corporation.
        $api_instance = new \nullx27\ESI\Api\CharacterApi();
        $characterId = $user->getId();
        
        try {
            $result = $api_instance->getCharactersCharacterId($characterId);
            $api_instance = new \nullx27\ESI\Api\CorporationApi();
            $corporationId = $result['corporationId'];
            try {
                $result = $api_instance->getCorporationsCorporationId($corporationId);
                echo 'Character is in ' . $result['corporationName'];
            } catch (Exception $e) {
                echo 'Exception when calling CorporationApi->getCorporationsCorporationId: ', $e->getMessage(), PHP_EOL;
            }
        } catch (Exception $e) {
            echo 'Exception when calling CharacterApi->getCharactersCharacterId: ', $e->getMessage(), PHP_EOL;
        }
    }
}
