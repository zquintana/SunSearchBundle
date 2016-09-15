<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document(index="my_core")
 */
class ValidTestEntityIndexProperty
{
    /**
     * @Sun\Id
     */
    private $id;

    /**
     *
     * @Sun\Field
     */
    private $title;
}

