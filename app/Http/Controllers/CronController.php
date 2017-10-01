<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Ixudra\Curl\Facades\Curl;
use App\User;
use App\Type;
use DB;
use App\Asset;

class CronController extends Controller
{

    public function refresh()
    {

        // Need to request a new valid access token from EVE SSO using the refresh token of the original request.
        $user = User::first();

        $url = 'https://login.eveonline.com/oauth/token';
        $response = Curl::to($url)
            ->withData(array(
                'grant_type' => "refresh_token",
                'refresh_token' => $user->refresh_token
            ))
            ->withHeaders(array(
                'Authorization: Basic ' . base64_encode(env('EVEONLINE_CLIENT_ID') . ':' . env('EVEONLINE_CLIENT_SECRET'))
            ))
            ->enableDebug('logFile.txt')
            ->post();
        
        // If that worked, let's request the corporation assets.
        // TODO: This will have to loop or make recursive calls to handle multiple pages.
        if ($response)
        {
            $new_token = json_decode($response);
            $api_instance = new \nullx27\ESI\Api\CharacterApi();
            $characterId = $user->eve_id;
            
            try {
                $result = $api_instance->getCharactersCharacterId($characterId);
                $corporationId = $result['corporationId'];
                try {
                    $url = 'https://esi.tech.ccp.is/latest/corporations/' . $corporationId . '/assets/';
                    $response = Curl::to($url)
                        ->withData(array(
                            'token' => $new_token->access_token
                        ))
                        ->enableDebug('logFile.txt')
                        ->get();
                    if ($response)
                    {
                        $assets = json_decode($response);
                        foreach ($assets as $item)
                        {
                            $asset = Asset::find($item->type_id);

                            if ($asset)
                            {
                                $asset->quantity = (isset($item->quantity) && $item->quantity > 0) ? $asset->quantity + $item->quantity : $asset->quantity + 1;
                            }
                            else
                            {
                                $asset = new Asset;
                                $asset->typeID = $item->type_id;
                                $asset->location = $item->location_id;
                                $asset->quantity = (isset($item->quantity) && $item->quantity > 0) ? $item->quantity : 1;
                            }

                            $asset->save();
                        }
                    }
                } catch (Exception $e) {
                    echo 'Exception when calling ' . $url, $e->getMessage(), PHP_EOL;
                }
            } catch (Exception $e) {
                echo 'Exception when calling CharacterApi->getCharactersCharacterId: ', $e->getMessage(), PHP_EOL;
            }
        }

    }

}
