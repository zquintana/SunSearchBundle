<?php

namespace ZQ\SunSearchBundle\Query;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;
use ZQ\SunSearchBundle\Client\SunClient;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 * Class AbstractQuery
 */
abstract class AbstractQuery extends Query
{
    /**
     * @var Document
     */
    protected $document = null;

    /**
     *
     * @var SunClient
     */
    protected $sunSearch = null;

    /**
     * @var string
     */
    protected $index = null;

    /**
     * @var object
     */
    private $entity = null;

    /**
     * @var MetaInformationInterface
     */
    private $metaInformation;

    /**
     * @var \Solarium\QueryType\Select\Query\Query
     */
    private $selectQuery;


    /**
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function getSelectQuery()
    {
        if (!$this->selectQuery) {
            $this->selectQuery = $this->solr->getSelectQuery($this);
        }

        return $this->selectQuery;
    }

    /**
     * @return MetaInformationInterface
     */
    public function getMetaInformation()
    {
        return $this->metaInformation;
    }

    /**
     * @param MetaInformationInterface $metaInformation
     */
    public function setMetaInformation($metaInformation)
    {
        $this->metaInformation = $metaInformation;

        $this->entity = $metaInformation->getEntity();
        $this->index = $metaInformation->getIndex();
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param object $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param \Solarium\QueryType\Update\Query\Document\Document $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return \Solarium\QueryType\Update\Query\Document\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return SunClient
     */
    public function getSunSearch()
    {
        return $this->sunSearch;
    }

    /**
     * @param SunClient $sunSearch
     *
     * @return AbstractQuery
     */
    public function setSunSearch($sunSearch)
    {
        $this->sunSearch = $sunSearch;

        return $this;
    }

    /**
     * modes defined in ZQ\SunSearchBundle\Doctrine\Hydration\HydrationModes
     *
     * @param string $mode
     */
    public function setHydrationMode($mode)
    {
        $this->getSunSearch()->getMapper()->setHydrationMode($mode);
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }
}
