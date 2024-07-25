<?php

namespace AverianovL\GroomingBot\Service\Review;

use AverianovL\GroomingBot\Service\Review\ContentProvider\CaptionProvider;
use AverianovL\GroomingBot\Service\Review\ContentProvider\ImageProvider;
use AverianovL\GroomingBot\Service\Review\Object\Review;

class ReviewGenerator
{
    public static function make(): Review
    {
        $review = new Review();
        $review->image = ImageProvider::getImage();
        $review->caption = CaptionProvider::make($review->image);

        return $review;
    }
}