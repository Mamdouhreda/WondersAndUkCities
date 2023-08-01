<?php

// Define the namespace
namespace modules\search\controllers;

// Import the necessary classes
use Craft;
use craft\web\Controller;
use yii\web\Response;
use GuzzleHttp\Client;

// Extend the base Controller class
class SearchController extends Controller
{
    protected array|bool|int $allowAnonymous = true;
    
    // Define an action method for searching
    public function actionSearch(): Response
    {
        $this->requirePostRequest();

        // Get the city parameter from the request body
        $city = Craft::$app->getRequest()->getBodyParam('city');

        // Get the API key from the environment variable
        $apiKey = getenv('API_KEY');

        // Generate a cache key based on the city name
        $cacheKey = 'googlePlaces_' . $city;

        // Check if the data is already cached
        $results = Craft::$app->cache->get($cacheKey);

        if (!$results) {
            // Initialize a Guzzle HTTP client
            $client = new Client();

            // Make a GET request to the Google Places API
            $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                'query' => [
                    'input' => $city,
                    'types' => '(cities)',
                    'components' => 'country:uk',
                    'key' => $apiKey,
                ],
            ]);

            // Decode the JSON response body
            $data = json_decode($response->getBody(), true);

            // If no predictions were found, return an error
            if (!isset($data['predictions']) || count($data['predictions']) == 0) {
                return $this->asJson([
                    'error' => 'No results found for the given input. Please enter a valid UK city name.',
                ]);
            }

            // Format the predictions for return
            $results = array_map(function ($prediction) {
                return ['description' => $prediction['description'], 'place_id' => $prediction['place_id']];
            }, $data['predictions']);

            // Cache the results
            Craft::$app->cache->set($cacheKey, $results);
        }

        // Return the predictions as a JSON response
        return $this->asJson([
            'predictions' => $results,
        ]);
    }

    // Define an action method for getting place details
    public function actionGetPlaceDetails(): Response
    {
        // Require the request to be a POST request
        $this->requirePostRequest();

        // Get the place_id parameter from the request body
        $placeId = Craft::$app->getRequest()->getBodyParam('place_id');

        // Get the API key from the environment variable
        $apiKey = getenv('API_KEY');

        // Generate a cache key based on the place ID
        $cacheKey = 'googlePlacesDetails_' . $placeId;

        // Check if the data is already cached
        $data = Craft::$app->cache->get($cacheKey);

        if (!$data) {
            // Initialize a Guzzle HTTP client
            $client = new Client();

            // Make a GET request to the Google Places API
            $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/details/json', [
                'query' => [
                    'place_id' => $placeId,
                    'fields' => 'name,geometry',
                    'key' => $apiKey,
                ],
            ]);

            // Decode the JSON response body
            $data = json_decode($response->getBody(), true);

            // Cache the data
            Craft::$app->cache->set($cacheKey, $data);
        }

        // If location details were found, return them as a JSON response
        if (isset($data['result']['geometry']['location'])) {
            $location = $data['result']['geometry']['location'];
            return $this->asJson([
                'city' => $data['result']['name'],
                'longitude' => $location['lng'],
                'latitude' => $location['lat'],
            ]);
        }

        // If location details were not found, return an error
        return $this->asJson([
            'error' => 'Failed to retrieve location details for the selected city.',
        ]);
    }
}
