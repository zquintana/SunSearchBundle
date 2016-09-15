<?php

namespace FS\SolrBundle\Query;

use Doctrine\Common\Collections\ArrayCollection;
use FS\SolrBundle\Doctrine\Mapper\EntityMapper;
use Solarium\QueryType\Select\Result\Result;

/**
 * Class ResultSet
 *
 * @package FS\SolrBundle\Query
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
