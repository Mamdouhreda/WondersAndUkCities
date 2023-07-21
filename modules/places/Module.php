<?php

namespace modules\places;

use Craft;
use yii\base\Module as BaseModule;
use GuzzleHttp\Client;

class Module extends BaseModule
{
    public function init(): void
    {
        Craft::setAlias('@modules/places', __DIR__);

        if (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'modules\\places\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\places\\controllers';
        }

        parent::init();

        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
    }

    public function getPlaces()
    {
        $apiKey = getenv('API_KEY');

        $placeIds = [
            'ChIJGymPrIdFWBQRJCSloj8vDIE', // The Great Pyramid of Giza
            'ChIJzyx_aNch8TUR3yIFlZslQNA', // Great Wall of China
            'ChIJrRMgU7ZhLxMRxAOFkC7I8Sg', // Colosseum
            'ChIJbf8C1yFxdDkR3n12P4DkKt0', // Taj Mahal
            'ChIJxfxkgTdvARURaDKtZ1zADSk',  // Petra
            'ChIJM_iYoLk4UY8RRQ11MHWmcA8'    //Chichén Itzá
        ];

        $client = new Client();

        $places = [];

        foreach ($placeIds as $placeId) {
            $response = $client->get("https://maps.googleapis.com/maps/api/place/details/json?placeid=$placeId&key=$apiKey");

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['result'])) {
                    $place = $data['result'];

                    $place['address'] = $this->getAddress($place); // Update with address

                    $photoUrl = $this->getPhotoUrl($place, $apiKey);
                    if ($photoUrl !== null) {
                        $place['photoUrl'] = $photoUrl;
                    }

                    $places[] = $place;
                }
            }
        }

        return $places;
    }

    private function getPhotoUrl(array $place, string $apiKey): ?string
    {
        if (!empty($place['photos'])) {
            $photoReference = $place['photos'][0]['photo_reference'];
            return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photo_reference=$photoReference&key=$apiKey";
        }

        return null;
    }

    private function getAddress(array $place): string
    {
        $addressComponents = $place['address_components'];
        $addressParts = [];

        foreach ($addressComponents as $component) {
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

        return implode(', ', $addressParts);
    }
}
