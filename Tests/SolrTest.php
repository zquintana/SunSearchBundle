<?php

namespace ZQ\SunSearchBundle\Tests;

use ZQ\SunSearchBundle\Solarium\QueryType\Entity\FindByDocumentNameQuery;
use ZQ\SunSearchBundle\Solarium\QueryType\Entity\SolrQuery;
use ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\InvalidTestEntityFiltered;
use ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\ValidTestEntityFiltered;
use ZQ\SunSearchBundle\Tests\Doctrine\Mapper\ValidTestEntity;
use ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\EntityWithRepository;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformation;
use ZQ\SunSearchBundle\Tests\Util\MetaTestInformationFactory;
use ZQ\SunSearchBundle\Client\SunClient;
use ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\ValidEntityRepository;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 *
 * @group facade
 */
class SolrTest extends AbstractSolrTest
{

    public function testCreateQuery_ValidEntity()
    {
        $this->setupMetaFactoryLoadOneCompleteInformation();

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $query = $solr->createEntityQuery('FSBlogBundle:ValidTestEntity');

        $this->assertTrue($query instanceof SolrQuery);
        $this->assertEquals(4, count($query->getMappedFields()));

    }

    public function testGetRepository_UserdefinedRepository()
    {
        $metaInformation = new MetaInformation();
        $metaInformation->setClassName(get_class(new EntityWithRepository()));
        $metaInformation->setRepository('ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\ValidEntityRepository');

        $this->setupMetaFactoryLoadOneCompleteInformation($metaInformation);

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $actual = $solr->getRepository('Tests:EntityWithRepository');

        $this->assertTrue($actual instanceof ValidEntityRepository);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetRepository_UserdefinedInvalidRepository()
    {
        $metaInformation = new MetaInformation();
        $metaInformation->setClassName(get_class(new EntityWithRepository()));
        $metaInformation->setRepository('ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\InvalidEntityRepository');

        $this->setupMetaFactoryLoadOneCompleteInformation($metaInformation);

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->getRepository('Tests:EntityWithInvalidRepository');
    }

    public function testAddDocument()
    {
        $this->assertUpdateQueryExecuted();

        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->mapOneDocument();

        $this->setupMetaFactoryLoadOneCompleteInformation();

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->addDocument(new ValidTestEntity());
    }

    public function testUpdateDocument()
    {
        $this->assertUpdateQueryExecuted();

        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->mapOneDocument();

        $this->setupMetaFactoryLoadOneCompleteInformation();

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->updateDocument(new ValidTestEntity());
    }

    public function testDoNotUpdateDocumentIfDocumentCallbackAvoidIt()
    {
        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        $this->assertUpdateQueryWasNotExecuted();

        $information = new MetaInformation();
        $information->setSynchronizationCallback('shouldBeIndex');
        $this->setupMetaFactoryLoadOneCompleteInformation($information);

        $filteredEntity = new ValidTestEntityFiltered();
        $filteredEntity->shouldIndex = false;

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->updateDocument($filteredEntity);
    }

    public function testRemoveDocument()
    {
        $this->assertDeleteQueryWasExecuted();

        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->setupMetaFactoryLoadOneCompleteInformation();

        $this->mapper->expects($this->once())
            ->method('toDocument')
            ->will($this->returnValue(new DocumentStub()));

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->removeDocument(new ValidTestEntity());
    }

    public function testClearIndex()
    {
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->solrClientFake->expects($this->once())
            ->method('getEndpoints')
            ->will($this->returnValue(array('core0' => array())));

        $this->assertDeleteQueryWasExecuted();

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->clearIndex();
    }

    public function testQuery_NoResponseKeyInResponseSet()
    {
        $document = new Document();
        $document->addField('document_name_s', 'name');

        $query = new FindByDocumentNameQuery();
        $query->setDocumentName('name');
        $query->setDocument($document);
        $query->setIndex('index0');

        $this->assertQueryWasExecuted(array(), 'index0');

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);

        $entities = $solr->query($query);
        $this->assertEquals(0, count($entities));
    }

    public function testQuery_OneDocumentFound()
    {
        $arrayObj = new DocumentStub(array('title_s' => 'title'));

        $document = new Document();
        $document->addField('document_name_s', 'name');

        $query = new FindByDocumentNameQuery();
        $query->setDocumentName('name');
        $query->setDocument($document);
        $query->setEntity(new ValidTestEntity());
        $query->setIndex('index0');

        $this->assertQueryWasExecuted(array($arrayObj), 'index0');

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);

        $entities = $solr->query($query);
        $this->assertEquals(1, count($entities));
    }

    public function testAddEntity_ShouldNotIndexEntity()
    {
        $this->assertUpdateQueryWasNotExecuted();

        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        $entity = new ValidTestEntityFiltered();

        $information = new MetaInformation();
        $information->setSynchronizationCallback('shouldBeIndex');
        $this->setupMetaFactoryLoadOneCompleteInformation($information);

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->addDocument($entity);

        $this->assertTrue($entity->getShouldBeIndexedWasCalled(), 'filter method was not called');
    }

    public function testAddEntity_ShouldIndexEntity()
    {
        $this->assertUpdateQueryExecuted('index0');

        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $entity = new ValidTestEntityFiltered();
        $entity->shouldIndex = true;

        $information = MetaTestInformationFactory::getMetaInformation();
        $information->setSynchronizationCallback('shouldBeIndex');
        $information->setIndex('index0');
        $this->setupMetaFactoryLoadOneCompleteInformation($information);

        $this->mapOneDocument();

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->addDocument($entity);

        $this->assertTrue($entity->getShouldBeIndexedWasCalled(), 'filter method was not called');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testAddEntity_FilteredEntityWithUnknownCallback()
    {
        $this->assertUpdateQueryWasNotExecuted();

        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        $information = MetaTestInformationFactory::getMetaInformation();
        $information->setSynchronizationCallback('shouldBeIndex');
        $this->setupMetaFactoryLoadOneCompleteInformation($information);

        $solr = new SunClient($this->solrClientFake, $this->commandFactory, $this->eventDispatcher, $this->metaFactory, $this->mapper);
        $solr->addDocument(new InvalidTestEntityFiltered());
    }

}



