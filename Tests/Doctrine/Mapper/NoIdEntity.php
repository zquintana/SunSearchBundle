<?php
namespace ZQ\SunSearchBundle\Tests\Doctrine\Mapper;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 * @author Florian
 * @Sun\Index
 */
class NoIdEntity
{
    private $id;

    /**
     * @Sun\Field(type="string")
     * @var string
     */
    private $text;

    public function getId()
    {
        return $this->id;
    }
}

