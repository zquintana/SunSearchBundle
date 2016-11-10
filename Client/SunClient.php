<?php

namespace ZQ\SunSearchBundle\Client;

use Solarium\Client as SolrClient;
use Solarium\Plugin\BufferedAdd\BufferedAdd;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Update\Query\Document\Document;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ZQ\SunSearchBundle\Client\Solarium\SolariumMulticoreClient;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;
use ZQ\SunSearchBundle\Exception\HydratorException;
use ZQ\SunSearchBundle\Doctrine\Mapper\EntityMapper;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\CommandFactory;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationFactory;
use ZQ\SunSearchBundle\Event\ErrorEvent;
use ZQ\SunSearchBundle\Event\Event;
use ZQ\SunSearchBundle\Event\Events;
use ZQ\SunSearchBundle\Model\CoreManager;
use ZQ\SunSearchBundle\Repository\Repository;
use ZQ\SunSearchBundle\Solarium\QueryType\Entity\AbstractQuery;
use ZQ\SunSearchBundle\Solarium\QueryType\Entity\ResultSet;
use ZQ\SunSearchBundle\Solarium\QueryType\Entity\SolrQuery;

/**
 * Class allows to index doctrine entities
 */
class SunClient implements SunClientInterface
{
    /**
     * @var SolrClient
     */
    protected $solrClientCore;

    /**
     * @var EntityMapper
     */
    protected $entityMapper;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventManager;

    /**
     * @var MetaInformationFactory
     */
    protected $metaInformationFactory;

    /**
     * @var CoreManager
     */
    protected $coreManager;

    /**
     * @var int numFound
     */
    private $numberOfFoundDocuments = 0;


    /**
     * @param SolrClient               $client
     * @param CommandFactory           $commandFactory
     * @param EventDispatcherInterface $manager
     * @param MetaInformationFactory   $metaInformationFactory
     * @param EntityMapper             $entityMapper
     * @param CoreManager              $coreManager
     */
    public function __construct(
        SolrClient $client,
        CommandFactory $commandFactory,
        EventDispatcherInterface $manager,
        MetaInformationFactory $metaInformationFactory,
        EntityMapper $entityMapper,
        CoreManager $coreManager
    ) {
        $this->solrClientCore = $client;
        $this->commandFactory = $commandFactory;
        $this->eventManager = $manager;
        $this->metaInformationFactory = $metaInformationFactory;
        $this->entityMapper = $entityMapper;
        $this->coreManager  = $coreManager;
    }

    /**
     * @return SolrClient
     */
    public function getClient()
    {
        return $this->solrClientCore;
    }

    /**
     * @return EntityMapper
     */
    public function getMapper()
    {
        return $this->entityMapper;
    }

    /**
     * @return CommandFactory
     */
    public function getCommandFactory()
    {
        return $this->commandFactory;
    }

    /**
     * @return MetaInformationFactory
     */
    public function getMetaFactory()
    {
        return $this->metaInformationFactory;
    }

    /**
     * @return CoreManager
     */
    public function getCoreManager()
    {
        return $this->coreManager;
    }

    /**
     * @param object $entity
     *
     * @return SolrQuery
     */
    public function createEntityQuery($entity)
    {
        $metaInformation = $this->metaInformationFactory->loadInformation($entity);
        $class = $metaInformation->getClassName();
        $entity = new $class();

        $query = $this->createQuery($metaInformation->getIndex());
        $query->setEntity($entity);
        $query->setMappedFields($metaInformation->getFieldMapping());

        return $query;
    }

    /**
     * @param string $index
     *
     * @return SolrQuery
     */
    public function createQuery($index)
    {
        $query = new SolrQuery();
        $query->setSunSearch($this);
        $query->setIndex($index);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($entityAlias)
    {
        $metaInformation = $this->metaInformationFactory->loadInformation($entityAlias);
        $class = $metaInformation->getClassName();

        $entity = new $class();

        $repositoryClass = $metaInformation->getRepository();
        if (class_exists($repositoryClass)) {
            $repositoryInstance = new $repositoryClass($this, $entity);

            if ($repositoryInstance instanceof Repository) {
                return $repositoryInstance;
            }

            throw new \RuntimeException(sprintf(
                '%s must extends the ZQ\SunSearchBundle\Repository\Repository',
                $repositoryClass
            ));
        }

        return new Repository($this, $entity);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDocument($entity)
    {
        $command = $this->commandFactory->get('identifier');

        $this->entityMapper->setMappingCommand($command);

        $metaInformations = $this->metaInformationFactory->loadInformation($entity);

        if ($document = $this->entityMapper->toDocument($metaInformations)) {
            $event = new Event($this->solrClientCore, $metaInformations);
            $this->eventManager->dispatch(Events::PRE_DELETE, $event);

            try {
                $indexName = $metaInformations->getIndex();

                $client = new SolariumMulticoreClient($this->solrClientCore);

                $client->delete($document, $indexName);
            } catch (\Exception $e) {
                $errorEvent = new ErrorEvent(null, $metaInformations, 'delete-document', $event);
                $errorEvent->setException($e);

                $this->eventManager->dispatch(Events::ERROR, $errorEvent);
            }

            $this->eventManager->dispatch(Events::POST_DELETE, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addDocument($entity)
    {
        $metaInformation = $this->metaInformationFactory->loadInformation($entity);

        if (!$this->addToIndex($metaInformation, $entity)) {
            return false;
        }

        $doc = $this->toDocument($metaInformation);

        $event = new Event($this->solrClientCore, $metaInformation);
        $this->eventManager->dispatch(Events::PRE_INSERT, $event);

        $this->addDocumentToIndex($doc, $metaInformation, $event);

        $this->eventManager->dispatch(Events::POST_INSERT, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function computeChangeSet(array $doctrineChangeSet, $entity)
    {
        /* If not set, act the same way as if there are changes */
        if (empty($doctrineChangeSet)) {
            return array();
        }

        $metaInformation = $this->metaInformationFactory->loadInformation($entity);

        $documentChangeSet = array();

        /* Check all Solr fields on this entity and check if this field is in the change set */
        foreach ($metaInformation->getFields() as $field) {
            if (array_key_exists($field->name, $doctrineChangeSet)) {
                $documentChangeSet[] = $field->name;
            }
        }

        return $documentChangeSet;
    }

    /**
     * Get select query
     *
     * @param AbstractQuery $query
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function getSelectQuery(AbstractQuery $query)
    {
        $selectQuery = $this->solrClientCore->createSelect($query->getOptions());

        $selectQuery->setQuery($query->getQuery());
        $selectQuery->setFilterQueries($query->getFilterQueries());
        $selectQuery->setSorts($query->getSorts());
        $selectQuery->setFields($query->getFields());

        $facetSet = $query->getComponent(Query::COMPONENT_FACETSET);
        if ($facetSet) {
            $selectQuery->setComponent(Query::COMPONENT_FACETSET, $facetSet);
        }

        return $selectQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function query(AbstractQuery $query, $hydrationMode = 'doctrine')
    {
        $prevMode = $this->entityMapper->getHydrationMode();
        $this->entityMapper->setHydrationMode($hydrationMode);

        $entity = $query->getEntity();
        $core   = $query->getIndex();
        $endpoint    = $this->getCoreManager()->getEndpoint($core);
        $selectQuery = $this->getSelectQuery($query);

        try {
            $endpoint->setCore($core);
            $response = $this->solrClientCore->select($selectQuery, $endpoint);
            $results = new ResultSet($entity, $this->entityMapper, $response);
        } catch (HydratorException $e) {
            $errorEvent = new ErrorEvent(null, null, 'query solr');
            $errorEvent->setException($e);

            $this->eventManager->dispatch(Events::ERROR, $errorEvent);

            $results = new ResultSet($entity, null, null);
        }

        $endpoint->setCore(null);
        $this->entityMapper->setHydrationMode($prevMode);

        return $results;
    }

    /**
     * @param string $sQuery
     * @param string $index
     *
     * @return SolrQuery
     */
    public function selectQuery($sQuery, $index)
    {
        $query = $this->createQuery($index);
        $query->setCustomQuery($sQuery);

        return $query;
    }

    /**
     * Number of overall found documents for a given query
     *
     * @return integer
     */
    public function getNumFound()
    {
        return $this->numberOfFoundDocuments;
    }

    /**
     * clears the whole index by using the query *:*
     */
    public function clearIndex()
    {
        $this->eventManager->dispatch(Events::PRE_CLEAR_INDEX, new Event($this->solrClientCore));

        try {
            $client = new SolariumMulticoreClient($this->solrClientCore);
            $client->clearCores();
        } catch (\Exception $e) {
            $errorEvent = new ErrorEvent(null, null, 'clear-index');
            $errorEvent->setException($e);

            $this->eventManager->dispatch(Events::ERROR, $errorEvent);
        }

        $this->eventManager->dispatch(Events::POST_CLEAR_INDEX, new Event($this->solrClientCore));
    }

    /**
     * @param array $entities
     */
    public function synchronizeIndex($entities)
    {
        /** @var BufferedAdd $buffer */
        $buffer = $this->solrClientCore->getPlugin('bufferedadd');
        $buffer->setBufferSize(500);

        foreach ($entities as $entity) {
            $metaInformations = $this->metaInformationFactory->loadInformation($entity);

            if (!$this->addToIndex($metaInformations, $entity)) {
                continue;
            }

            $doc = $this->toDocument($metaInformations);

            $buffer->addDocument($doc);
        }

        $buffer->commit();
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function updateDocument($entity)
    {
        $metaInformations = $this->metaInformationFactory->loadInformation($entity);

        if (!$this->addToIndex($metaInformations, $entity)) {
            return false;
        }

        $doc = $this->toDocument($metaInformations);

        $event = new Event($this->solrClientCore, $metaInformations);
        $this->eventManager->dispatch(Events::PRE_UPDATE, $event);

        $this->addDocumentToIndex($doc, $metaInformations, $event);

        $this->eventManager->dispatch(Events::POST_UPDATE, $event);

        return true;
    }

    /**
     * @param MetaInformationInterface $metaInformation
     *
     * @return Document
     */
    private function toDocument(MetaInformationInterface $metaInformation)
    {
        $command = $this->commandFactory->get('all');

        $this->entityMapper->setMappingCommand($command);
        $doc = $this->entityMapper->toDocument($metaInformation);

        return $doc;
    }

    /**
     * @param object                   $doc
     * @param MetaInformationInterface $metaInformation
     * @param Event                    $event
     */
    private function addDocumentToIndex($doc, MetaInformationInterface $metaInformation, Event $event)
    {
        try {
            $indexName = $metaInformation->getIndex();

            $client = new SolariumMulticoreClient($this->solrClientCore);
            $client->update($doc, $indexName);

        } catch (\Exception $e) {
            $errorEvent = new ErrorEvent(null, $metaInformation, json_encode($this->solrClientCore->getOptions()), $event);
            $errorEvent->setException($e);

            $this->eventManager->dispatch(Events::ERROR, $errorEvent);
        }
    }

    /**
     * @param MetaInformationInterface $metaInformation
     * @param object                   $entity
     *
     * @return boolean
     *
     * @throws \BadMethodCallException if callback method not exists
     */
    private function addToIndex(MetaInformationInterface $metaInformation, $entity)
    {
        if (!$metaInformation->hasSynchronizationFilter()) {
            return true;
        }

        $callback = $metaInformation->getSynchronizationCallback();
        if (!method_exists($entity, $callback)) {
            throw new \BadMethodCallException(sprintf('unknown method %s in entity %s', $callback, get_class($entity)));
        }

        return $entity->$callback();
    }
}
