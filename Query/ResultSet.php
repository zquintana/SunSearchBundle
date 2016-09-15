<?php

namespace ZQ\SunSearchBundle\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Solarium\QueryType\Select\Result\Result;
use ZQ\SunSearchBundle\Doctrine\Mapper\EntityMapper;

/**
 * Class ResultSet
 */
class ResultSet extends ArrayCollection
{
    /**
     * @var Result
     */
    private $response;


    /**
     * ResultSet constructor.
     *
     * @param object            $entity
     * @param EntityMapper|null $mapper
     * @param Result|null       $response
     */
    public function __construct($entity, EntityMapper $mapper = null, Result $response = null)
    {
        if ($mapper === null || $response === null) {
            return;
        }

        $mappedEntities = array();
        foreach ($response as $document) {
            $mappedEntities[] = $mapper->toEntity($document, $entity);
        }

        $this->response = $response;

        parent::__construct($mappedEntities);
    }

    /**
     * Response getter
     *
     * @return Result
     */
    public function getResponse()
    {
        return $this->response;
    }
}
