<?php

namespace ZQ\SunSearchBundle\Repository;

use ZQ\SunSearchBundle\Client\SunSunClient;
use ZQ\SunSearchBundle\Doctrine\Hydration\HydrationModes;
use ZQ\SunSearchBundle\Query\FindByDocumentNameQuery;
use ZQ\SunSearchBundle\Query\FindByIdentifierQuery;

/**
 * Common repository class to find documents in the index
 */
class Repository implements RepositoryInterface
{

    /**
     * @var SunSunClient
     */
    protected $sunClient = null;

    /**
     * @var object
     */
    protected $entity = null;

    /**
     * @var string
     */
    protected $hydrationMode = '';

    /**
     * @param SunSunClient $sunClient
     * @param object $entity
     */
    public function __construct(SunSunClient $sunClient, $entity)
    {
        $this->sunClient = $sunClient;
        $this->entity = $entity;

        $this->hydrationMode = HydrationModes::HYDRATE_DOCTRINE;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $mapper = $this->sunClient->getMapper();
        $mapper->setMappingCommand($this->sunClient->getCommandFactory()->get('all'));
        $metaInformation = $this->sunClient->getMetaFactory()->loadInformation($this->entity);
        $metaInformation->setEntityId($id);

        $document = $mapper->toDocument($metaInformation);

        $query = new FindByIdentifierQuery();
        $query->setIndex($metaInformation->getIndex());
        $query->setDocumentKey($metaInformation->getDocumentKey());
        $query->setDocument($document);
        $query->setEntity($this->entity);
        $query->setSolr($this->sunClient);
        $query->setHydrationMode($this->hydrationMode);
        $found = $this->sunClient->query($query);

        if (count($found) == 0) {
            return null;
        }

        return array_pop($found);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $mapper = $this->sunClient->getMapper();
        $mapper->setMappingCommand($this->sunClient->getCommandFactory()->get('all'));
        $metaInformation = $this->sunClient->getMetaFactory()->loadInformation($this->entity);

        $document = $mapper->toDocument($metaInformation);

        if (null === $document) {
            return null;
        }

        $document->removeField('id');

        $query = new FindByDocumentNameQuery();
        $query->setRows(1000000);
        $query->setDocumentName($metaInformation->getDocumentName());
        $query->setIndex($metaInformation->getIndex());
        $query->setDocument($document);
        $query->setEntity($this->entity);
        $query->setSolr($this->sunClient);
        $query->setHydrationMode($this->hydrationMode);

        return $this->sunClient->query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $args)
    {
        $metaInformation = $this->sunClient->getMetaFactory()->loadInformation($this->entity);

        $query = $this->sunClient->createQuery($this->entity);
        $query->setHydrationMode($this->hydrationMode);
        $query->setRows(100000);
        $query->setUseAndOperator(true);
        $query->addSearchTerm('id', $metaInformation->getDocumentName(). '_*');
        $query->setQueryDefaultField('id');

        $helper = $query->getHelper();
        foreach ($args as $fieldName => $fieldValue) {
            $fieldValue = $helper->escapeTerm($fieldValue);

            $query->addSearchTerm($fieldName, $fieldValue);
        }

        return $this->sunClient->query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $args)
    {
        $metaInformation = $this->sunClient->getMetaFactory()->loadInformation($this->entity);

        $query = $this->sunClient->createQuery($this->entity);
        $query->setHydrationMode($this->hydrationMode);
        $query->setRows(1);
        $query->setUseAndOperator(true);
        $query->addSearchTerm('id', $metaInformation->getDocumentName(). '_*');
        $query->setQueryDefaultField('id');

        $helper = $query->getHelper();
        foreach ($args as $fieldName => $fieldValue) {
            $fieldValue = $helper->escapeTerm($fieldValue);

            $query->addSearchTerm($fieldName, $fieldValue);
        }

        $found = $this->sunClient->query($query);

        return array_pop($found);
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery()
    {
        return $this->sunClient->createQuery($this->entity);
    }
}
