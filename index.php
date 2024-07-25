<?php

use AverianovL\GroomingBot\Service\MessageGenerator;
use AverianovL\GroomingBot\Service\Providers\ImgurClientProvider;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

const ROOT = __DIR__;

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.local');
$dotenv->load();


$requestPath = explode('?', $_SERVER['REQUEST_URI']);
parse_str($requestPath[1] ?? '', $parsed);

try {
    (require_once ROOT . '/src/System/Function/controller_loader.php')($requestPath[0], $parsed);
} catch (\Throwable $e) {
    $client = new Nutgram($_ENV['TELEGRAM_BOT_TOKEN']);

    $text = mb_strimwidth(
        "Alert!\n"
        . $e->getMessage()
        . PHP_EOL
        . $e->getFile() . " on line " . $e->getLine()
        . PHP_EOL . PHP_EOL
        . "<code>" . $e->getTraceAsString() . "</code>",
        0,
        1024
    );

    $client->sendMessage(
        text: $text,
        chat_id: $_ENV['ADMIN_CHAT_ID'],
    );
}

