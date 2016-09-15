<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document
 */
class ValidTestEntityNoTypes
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

