<?php

namespace AverianovL\GroomingBot\Service;


use AverianovL\GroomingBot\Service\Providers\ImgurClientProvider;
use Imgur\Api\Gallery;
use GuzzleHttp\Client as Guzzle;
use OpenAI\Responses\Chat\CreateResponseChoice;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class MessageGenerator
{
    public static function Generate(): void {
        $client = new Nutgram($_ENV['TELEGRAM_BOT_TOKEN']);
        $client->setRunningMode(Webhook::class);

        $client->onCommand('review', function (Nutgram $bot) {
            // check lock
            if (file_exists(ROOT . '/tmp/bot.lock')) {
                $bot->sendMessage('Отзыв в процессе, ожидайте.');
                return;
            }

            // create lock
            file_put_contents(ROOT . '/tmp/bot.lock', 1);

//            // 1. get dog images
            $img = self::getImage();
//
//            // 2. get description from openapi
            $description = self::getDescription(
                base64_encode(file_get_contents($img))
            );
//
//            // 3. Send via telegram sprintf('Hello %s', $bot->message()->from->username))
            $bot->sendPhoto(
                InputFile::make(fopen($img, 'r')),
                caption: $description,
            );

            // remove lock
            unlink(ROOT . '/tmp/bot.lock');
        });

        $client->run();
    }

    private static function getDescription(mixed $base64Image): string
    {
        if (!base64_decode($base64Image, true)) {
            throw new \LogicException("image must be base64, got $base64Image");
        }

        $client = \OpenAI::client($_ENV['OPENAPI_TOKEN']);

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Напиши описание к картинке так, словно ты контент-менеджер груминг салона "Алмазные псы" и на фотографии - работа вашего мастера',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,$base64Image",
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $res = '';

        /** @var CreateResponseChoice $choice */
        foreach ($response->choices as $choice) {
            $res .= $choice->message->content;
        }

        return $res;
    }

    private static function getImage(): mixed
    {
        $imgurClient = ImgurClientProvider::get();

        $getLinkIfImage = static function ($item): ?string {
            if (in_array($item['type'] ?? false, ['image/jpeg', 'image/png'])) {
                return $item['link'] ?? null;
            }

            return null;
        };

        $searchResults =  $imgurClient->api('gallery')->search('grooming dog', 'top');
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
        $img = $filtered[rand(0, count($filtered) - 1)];

//        $img = (new Guzzle())->request('GET', $img, [
//                'headers' => [
//                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
//                    'Accept-Language' => 'ru-US,ru;q=0.9,en-US;q=0.8,en;q=0.7,ru-RU;q=0.6',
//                    'Priority' => 'u=1, i',
//                    'Referer' => 'http://localhost/',
//                    'Sec-CH-UA' => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
//                    'Sec-CH-UA-Mobile' => '?0',
//                    'Sec-CH-UA-Platform' => '"Linux"',
//                    'Sec-Fetch-Dest' => 'image',
//                    'Sec-Fetch-Mode' => 'no-cors',
//                    'Sec-Fetch-Site' => 'cross-site',
//                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
//                ]
//        ]);
//
//        if ($img->getStatusCode() != 200) {
//            throw new \LogicException('Image not available');
//        }


        return $img;
    }
}


