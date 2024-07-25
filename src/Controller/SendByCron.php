<?php

namespace AverianovL\GroomingBot\Controller;

use AverianovL\GroomingBot\Service\Review\ReviewGenerator;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class SendByCron
{
    public static function index()
    {
        $bot = new Nutgram($_ENV['TELEGRAM_BOT_TOKEN']);

        $review = ReviewGenerator::make();

        $bot->sendPhoto(
            InputFile::make($review->image->toResource()),
            chat_id: $_ENV['GROOMING_CHAT_ID'],
            caption: $review->caption,
        );
    }
}