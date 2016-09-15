<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document
 * @Sun\SynchronizationFilter(callback="shouldBeIndex")
 */
class ValidTestEntityFiltered
{
    private $shouldBeIndexedWasCalled = false;

    public $shouldIndex = false;

    public function shouldBeIndex()
    {
        $this->shouldBeIndexedWasCalled = true;

        return $this->shouldIndex;
    }

    public function getShouldBeIndexedWasCalled()
    {
        return $this->shouldBeIndexedWasCalled;
    }
}

