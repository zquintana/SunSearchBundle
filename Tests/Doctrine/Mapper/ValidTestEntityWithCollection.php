<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use ZQ\SunSearchBundle\Doctrine\Annotation as Sun;

/**
 *
 * @Sun\Document(boost="1")
 */
class ValidTestEntityWithCollection
{

    /**
     * @Sun\Id
     */
    private $id;

    /**
     * @Sun\Field(type="text")
     *
     * @var text
     */
    private $text;

    /**
     * @Sun\Field()
     *
     * @var text
     */
    private $title;

    /**
     * @Sun\Field(type="date")
     *
     * @var date
     */
    private $created_at;

    /**
     * @var ArrayCollection
     *
     * @Sun\Field(type="strings", getter="getTitle")
     */
    private $collection;

    /**
     * @Sun\Field(type="my_costom_fieldtype")
     *
     * @var string
     */
    private $costomField;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return the $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \FS\BlogBundle\Tests\Solr\Doctrine\Mapper\text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param \FS\BlogBundle\Tests\Solr\Doctrine\Mapper\text $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $costomField
     */
    public function setCostomField($costomField)
    {
        $this->costomField = $costomField;
    }

    /**
     * @return string
     */
    public function getCostomField()
    {
        return $this->costomField;
    }

    /**
     * @return \ZQ\SunSearchBundle\Tests\Doctrine\Mapper\date
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \ZQ\SunSearchBundle\Tests\Doctrine\Mapper\date $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return ArrayCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param ArrayCollection $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }


}

