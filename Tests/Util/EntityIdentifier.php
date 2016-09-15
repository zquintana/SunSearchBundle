<?php

namespace ZQ\SunSearchBundle\Tests\Util;

/**
 * Class EntityIdentifier
 */
class EntityIdentifier
{
    public static function generate()
    {
        return rand(1, 100000000);
    }
} 