<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class AppController extends Controller
{
    /**
     * App homepage. Check if the user is currently signed in, and either show
     * a signin prompt or the homepage.
     *
     * @return Response
     */
    public function home()
    {
        // If no current logged in user, show the login page.
        if (!Auth::check())
        {
            return view('login');
        }

        // User is authenticated!
        // Let's use their token to retrieve corporation assets.
        \nullx27\ESI\Configuration::getDefaultConfiguration()->setAccessToken(Auth::user()->token);

        $api_instance = new \nullx27\ESI\Api\CharacterApi();
        $characterId = Auth::user()->eve_id;
        
        try {
            $result = $api_instance->getCharactersCharacterId($characterId);
            $corporationId = $result['corporationId'];
            try {
                $url = 'https://esi.tech.ccp.is/latest/corporations/' . $corporationId . '/assets/';
                $response = Curl::to($url)
                    ->withData(array(
                        'token' => Auth::user()->token
                    ))
                    ->enableDebug('logFile.txt')
                    ->get();
                if ($response)
                {
                    $assets = json_decode($response);
                    foreach ($assets as $item)
                    {
                        echo $item->type_id . '<br>';
                    }
                }
            } catch (Exception $e) {
                echo 'Exception when calling ' . $url, $e->getMessage(), PHP_EOL;
            }
        } catch (Exception $e) {
            echo 'Exception when calling CharacterApi->getCharactersCharacterId: ', $e->getMessage(), PHP_EOL;
        }

        return view('home', [
            'user' => Auth::user(),
        ]);

    }

    /**
     * Logout the currently authenticated user.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}