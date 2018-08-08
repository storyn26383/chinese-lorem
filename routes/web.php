<?php

use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/[{length}]', function ($length = 100) use ($router) {
    $dictionary = app('cache')->remember('dictionary', 3600, function () {
        $client = new Client;
        $response = $client->get('http://data.coa.gov.tw/Service/OpenData/ODwsv/ODwsvTravelFood.aspx');

        return implode(array_map(function ($data) {
            return trim(preg_replace('/\s|　/', '', strip_tags($data)));
        }, array_column(json_decode($response->getBody()->getContents()), 'FoodFeature')));
    });

    return mb_substr($dictionary, rand(0, mb_strlen($dictionary) - $length), $length);
});
