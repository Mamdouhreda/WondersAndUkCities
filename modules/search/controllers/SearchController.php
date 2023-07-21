<?php

namespace modules\search\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use GuzzleHttp\Client;

class SearchController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionSearch(): Response
    {
        $this->requirePostRequest();
        $city = Craft::$app->getRequest()->getBodyParam('city');

        // Get the API key from the environment variable
        $apiKey = getenv('API_KEY');

        $client = new Client();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => [
                'query' => $city,
                'key' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if results are present in the response
        if (!isset($data['results']) || count($data['results']) == 0) {
            return $this->asJson([
                'error' => 'No results found for the given city. Please enter a valid UK city name.',
            ]);
        }

        // Check if any result has "United Kingdom" or "UK" in the formatted_address
        $ukResults = array_filter($data['results'], function ($result) {
            return strpos($result['formatted_address'], 'United Kingdom') !== false ||
                strpos($result['formatted_address'], 'UK') !== false;
        });

        // Check if there are any UK results
        if (count($ukResults) === 0) {
            return $this->asJson([
                'error' => 'No results found for the given city in the UK. Please enter a valid UK city name.',
            ]);
        }

        // Get the first UK result
        $firstResult = reset($ukResults);
        $location = $firstResult['geometry']['location'];

        return $this->asJson([
            'city' => $firstResult['formatted_address'],
            'longitude' => $location['lng'],
            'latitude' => $location['lat'],
        ]);
    }
}
