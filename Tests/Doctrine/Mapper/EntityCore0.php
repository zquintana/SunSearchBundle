<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Mapper;

use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 * @Sun\Document(index="core0")
 */
class EntityCore0
{
    /**
     * @Sun\Id
     */
    private $id;

    /**
     * @Sun\Field(type="text")
     *
     * @var string
     */
    private $text;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


}