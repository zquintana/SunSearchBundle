<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document(boost="aaaa")
 */
class ValidTestEntityWithInvalidBoost
{

    /**
     * @Sun\Id
     */
    private $id;
}

