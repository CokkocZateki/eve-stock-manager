<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Asset;
use App\Station;

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
        $assets = Asset::join('invTypes', 'assets.typeID', '=', 'invTypes.typeID')->orderBy('quantity', 'desc')->get();

        \nullx27\ESI\Configuration::getDefaultConfiguration()->setAccessToken(Auth::user()->token);

        foreach ($assets as $asset)
        {
            $station = Station::find($asset->location);
            if ($station)
            {
                $asset->location_name = $station->stationName;
            }
            else
            {
                $api_instance = new \nullx27\ESI\Api\UniverseApi();
                $structureId = $asset->location;
                
                try {
                    $result = $api_instance->getUniverseStructuresStructureId($structureId);
                    $asset->location_name = $result['name'];
                } catch (Exception $e) {
                    echo 'Exception when calling UniverseApi->getUniverseStructuresStructureId: ', $e->getMessage(), PHP_EOL;
                    $asset->location_name = '---';
                }
            }
        }

        return view('home', [
            'user' => Auth::user(),
            'assets' => $assets,
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