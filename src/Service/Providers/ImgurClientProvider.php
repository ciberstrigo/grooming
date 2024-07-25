<?php

namespace AverianovL\GroomingBot\Service\Providers;

use Imgur\Client;

class ImgurClientProvider
{
    public static function get(): Client
    {
        $client = new Client();
        $client->setOption('client_id', $_ENV['IMGUR_CLIENT_ID']);
        $client->setOption('client_secret', $_ENV['IMGUR_CLIENT_SECRET']);

        if (file_exists(ROOT . '/tmp/imgur.token')) {
            $imgurToken = json_decode(file_get_contents(ROOT . '/tmp/imgur.token'), true);
            $client->setAccessToken($imgurToken);

            if ($client->checkAccessTokenExpired()) {
                $imgurToken = $client->refreshToken();

                file_put_contents(ROOT . '/tmp/imgur.token', json_encode($imgurToken, JSON_PRETTY_PRINT));
            }
        }

        return $client;
    }
}