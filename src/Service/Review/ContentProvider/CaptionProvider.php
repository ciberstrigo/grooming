<?php

namespace AverianovL\GroomingBot\Service\Review\ContentProvider;

use AverianovL\GroomingBot\Service\Providers\OpenAIProvider;
use AverianovL\GroomingBot\Service\Review\Object\Image;

class CaptionProvider
{
    public static function make(Image $image): string
    {
        $client = OpenAIProvider::get();
        $base64Image = $image->toBase64();

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

        $caption = '';

        foreach ($response->choices as $choice) {
            $caption .= $choice->message->content;
        }

        return $caption;
    }
}