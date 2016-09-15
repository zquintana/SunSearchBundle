<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document
 * @Sun\SynchronizationFilter(callback="shouldBeIndex")
 */
class ValidTestEntityNumericFields
{

    /**
     *
     * @Sun\Field(type="integer")
     */
    private $integer;

    /**
     *
     * @Sun\Field(type="double")
     */
    private $double;

    /**
     *
     * @Sun\Field(type="float")
     */
    private $float;

    /**
     *
     * @Sun\Field(type="long")
     */
    private $long;
}

