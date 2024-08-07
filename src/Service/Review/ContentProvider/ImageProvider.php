<?php

namespace AverianovL\GroomingBot\Service\Review\ContentProvider;

use AverianovL\GroomingBot\Service\Providers\ImgurClientProvider;
use AverianovL\GroomingBot\Service\Review\Object\Image;
use GuzzleHttp\Client as Guzzle;

class ImageProvider
{
    const REQUEST_TEST = 'grooming dog';
    const REQUEST_SORT = 'top';

    public static function getImage(): Image
    {
        try {
            $imgurClient = ImgurClientProvider::get();

            $getLinkIfImage = static function ($item): ?string {
                if (in_array($item['type'] ?? false, ['image/jpeg', 'image/png'])) {
                    return $item['link'] ?? null;
                }

                return null;
            };

            $searchResults =  $imgurClient->api('gallery')->search(self::REQUEST_TEST, self::REQUEST_SORT);
            // filter to images only
            $filtered = [];
            foreach ($searchResults as $searchResult) {
                $filtered[] = $getLinkIfImage($searchResult);

                if ($searchResult['is_album'] ?? false) {
                    $albumImages = $imgurClient->api('album')->albumImages($searchResult['id']);

                    foreach ($albumImages as $albumImage) {
                        $filtered[] = $getLinkIfImage($albumImage);
                    }
                }
            }

            $filtered = array_merge(array_filter($filtered));

            return new Image($filtered[rand(0, count($filtered) - 1)]);
        } catch (\Throwable $e) {
            $apiResultRaw = (new Guzzle())->request('GET', 'https://dog.ceo/api/breeds/image/random', [])->getBody();
            $apiResult = json_decode((string) $apiResultRaw, true);

            return new Image($apiResult['message']);
        }
    }
}