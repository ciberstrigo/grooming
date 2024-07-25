<?php

namespace AverianovL\GroomingBot\System;

class CommandCallLimiter
{
    public const LIMIT = 1 * 60; // 5 minutes

    public static function commandCalled(string $by)
    {
        $table = json_decode(file_get_contents(ROOT . '/tmp/timestamps.json'), true);
        $table[$by] = time();
        file_put_contents(ROOT . '/tmp/timestamps.json', json_encode($table, JSON_PRETTY_PRINT));
    }

    public static function secondsBeforeUnlock(string $by)
    {
        $table = json_decode(file_get_contents(ROOT . '/tmp/timestamps.json'), true);

        if ($table[$by] + self::LIMIT > time()) {
            return $table[$by] + self::LIMIT - time();
        }

        return 0;
    }
}