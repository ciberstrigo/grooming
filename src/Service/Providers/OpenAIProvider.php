<?php

namespace AverianovL\GroomingBot\Service\Providers;

class OpenAIProvider
{
    public static function get()
    {
        return \OpenAI::client($_ENV['OPENAPI_TOKEN']);
    }
}