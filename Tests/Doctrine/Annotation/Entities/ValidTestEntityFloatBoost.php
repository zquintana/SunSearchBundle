<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document(boost="1.4")
 */
class ValidTestEntityFloatBoost
{

    /**
     * @Sun\Id
     */
    private $id;

}

