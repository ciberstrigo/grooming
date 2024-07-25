<?php

namespace AverianovL\GroomingBot\Controller;

use AverianovL\GroomingBot\Service\Review\ReviewGenerator;
use AverianovL\GroomingBot\System\CommandCallLimiter;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class WebhookController
{
    public static function index(): void
    {
        $client = new Nutgram($_ENV['TELEGRAM_BOT_TOKEN']);
        $client->setRunningMode(Webhook::class);

        $client->onCommand('review', function (Nutgram $bot) {
            if ($seconds = CommandCallLimiter::secondsBeforeUnlock($bot->userId())) {
                $bot->sendMessage("Вы можете запросить отзыв через $seconds секунд.");
                return;
            }

            CommandCallLimiter::commandCalled($bot->userId());
            $review = ReviewGenerator::make();

            $bot->sendPhoto(
                InputFile::make($review->image->toResource()),
                caption: $review->caption,
            );
        });

        $client->onCommand('getId', function (Nutgram $bot) {
            $bot->sendMessage($bot->message()->chat->id);
        });

        $client->run();

        echo 'webhook';
    }
}