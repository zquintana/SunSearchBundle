<?php
namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document
 * @Sun\SynchronizationFilter(callback="shouldBeIndex")
 */
class InvalidTestEntityFiltered
{
}

