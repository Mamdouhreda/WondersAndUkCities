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
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/autocomplete/json', [
            'query' => [
                'input' => $city,
                'types' => '(cities)',
                'components' => 'country:uk',
                'key' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['predictions']) || count($data['predictions']) == 0) {
            return $this->asJson([
                'error' => 'No results found for the given input. Please enter a valid UK city name.',
            ]);
        }

        // Return the formatted addresses of the matched predictions and the place_id
        $results = array_map(function ($prediction) {
            return ['description' => $prediction['description'], 'place_id' => $prediction['place_id']];
        }, $data['predictions']);

        return $this->asJson([
            'predictions' => $results,
        ]);
    }

    public function actionGetPlaceDetails(): Response
    {
        $this->requirePostRequest();
        $placeId = Craft::$app->getRequest()->getBodyParam('place_id');

        // Get the API key from the environment variable
        $apiKey = getenv('API_KEY');

        $client = new Client();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/details/json', [
            'query' => [
                'place_id' => $placeId,
                'fields' => 'name,geometry',
                'key' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['result']['geometry']['location'])) {
            $location = $data['result']['geometry']['location'];
            return $this->asJson([
                'city' => $data['result']['name'],
                'longitude' => $location['lng'],
                'latitude' => $location['lat'],
            ]);
        }

        return $this->asJson([
            'error' => 'Failed to retrieve location details for the selected city.',
        ]);
    }
}
