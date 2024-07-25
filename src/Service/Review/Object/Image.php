<?php

namespace AverianovL\GroomingBot\Service\Review\Object;

class Image
{
    public function __construct(public readonly string $url)
    {
    }

    public function toResource()
    {
        return fopen($this->url, 'r');
    }

    public function toBase64(): string
    {
        return  base64_encode(file_get_contents($this->url));
    }
}