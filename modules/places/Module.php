<?php

namespace modules\places;

use Craft;
use yii\base\Module as BaseModule;
use GuzzleHttp\Client;

class Module extends BaseModule
{
    // Initialize the module
    public function init(): void
    {
        // Sets an alias for the module
        Craft::setAlias('@modules/places', __DIR__);

        // Check the type of request (console or web request)
        if (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'modules\\places\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\places\\controllers';
        }

        // Initialize the parent module
        parent::init();

        // Attach event handlers once the Craft application has been initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    // Function to attach event handlers, empty in this case
    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
    }

    // Function to get places using Google Places API
    public function getPlaces()
    {
        // Get the API key from environment variables
        $apiKey = getenv('API_KEY');

        // IDs of places
        $placeIds = [
            'ChIJGymPrIdFWBQRJCSloj8vDIE', // The Great Pyramid of Giza
            'ChIJzyx_aNch8TUR3yIFlZslQNA', // Great Wall of China
            'ChIJrRMgU7ZhLxMRxAOFkC7I8Sg', // Colosseum
            'ChIJbf8C1yFxdDkR3n12P4DkKt0', // Taj Mahal
            'ChIJxfxkgTdvARURaDKtZ1zADSk',  // Petra
            'ChIJM_iYoLk4UY8RRQ11MHWmcA8'    //Chichén Itzá
        ];

        // Create new Guzzle HTTP client
        $client = new Client();

        // Array to store places
        $places = [];

        // Loop through each place ID
        foreach ($placeIds as $placeId) {
            // Fetch data from Google Places API
            $response = $client->get("https://maps.googleapis.com/maps/api/place/details/json?placeid=$placeId&key=$apiKey");

            // Check if the response status code is 200 (success)
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['result'])) {
                    $place = $data['result'];

                    // Get the address of the place
                    $place['address'] = $this->getAddress($place);

                    // Get the photo URL of the place
                    $photoUrl = $this->getPhotoUrl($place, $apiKey);
                    if ($photoUrl !== null) {
                        $place['photoUrl'] = $photoUrl;
                    }

                    // Add the place to the places array
                    $places[] = $place;
                }
            }
        }

        // Return the places
        return $places;
    }

    // Function to get the photo URL of a place
    private function getPhotoUrl(array $place, string $apiKey): ?string
    {
        // Check if the place has photos
        if (!empty($place['photos'])) {
            $photoReference = $place['photos'][0]['photo_reference'];
            // Return the photo URL
            return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photo_reference=$photoReference&key=$apiKey";
        }
        // If the place has no photos, return null
        return null;
    }

    // Function to get the address of a place
    private function getAddress(array $place): string
    {
        $addressComponents = $place['address_components'];
        $addressParts = [];

        // Loop through each address component
        foreach ($addressComponents as $component) {
            // Check the types of each component and add to address parts
            if (in_array('street_number', $component['types'])) {
                $addressParts[] = $component['long_name'];
            }

            if (in_array('route', $component['types'])) {
                $addressParts[] = $component['long_name'];
            }

            if (in_array('locality', $component['types'])) {
                $addressParts[] = $component['long_name'];
            }

            if (in_array('administrative_area_level_1', $component['types'])) {
                $addressParts[] = $component['short_name'];
            }

            if (in_array('country', $component['types'])) {
                $addressParts[] = $component['long_name'];
            }

            if (in_array('postal_code', $component['types'])) {
                $addressParts[] = $component['long_name'];
            }
        }

        //  return the address parts
        return implode(', ', $addressParts);
    }
}
