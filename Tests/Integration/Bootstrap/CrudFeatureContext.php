<?php

namespace ZQ\SunSearchBundle\Tests\Integration\Bootstrap;

use Behat\Behat\Context\Context;
use ZQ\SunSearchBundle\Client\SunSunClient;
use ZQ\SunSearchBundle\Tests\Doctrine\Mapper\ValidTestEntity;
use ZQ\SunSearchBundle\Tests\Util\EntityIdentifier;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 * Class CrudFeatureContext
 */
class CrudFeatureContext extends FeatureContext
{
    /**
     * @var ValidTestEntity
     */
    private $entity;

    /**
     * @var SunSunClient
     */
    private $sunClient;

    const DOCUMENT_NAME = 'validtestentity';


    /**
     * @Given /^I have a Doctrine entity$/
     */
    public function iHaveADoctrineEntity()
    {
        $this->sunClient = $this->getSunInstance();

        $this->entity = new ValidTestEntity();
        $this->entity->setId(EntityIdentifier::generate());
        $this->entity->setText('a Text');
    }

    /**
     * @When /^I add this entity to Solr$/
     */
    public function iAddThisEntityToSolr()
    {
        $this->sunClient->addDocument($this->entity);
    }

    /**
     * @Then /^should no error occurre$/
     */
    public function shouldNoErrorOccurre()
    {
        $eventDispatcher = $this->getEventDispatcher();

        if ($eventDispatcher->errorsOccurred()) {
            throw new \RuntimeException(sprintf('error occurred while indexing: %s', $eventDispatcher->getOccurredErrors()));
        }

        $this->assertInsertSuccessful($this->entity->getId(), self::DOCUMENT_NAME);
    }

    /**
     * @When /^I update one attribute$/
     */
    public function iUpdateOneAttribute()
    {
        $this->entity->setText('text has changed');
    }

    /**
     * @Then /^the index should be updated$/
     */
    public function theIndexShouldBeUpdated()
    {
        $entityId = $this->entity->getId();
        $document = $this->findDocumentById($entityId, self::DOCUMENT_NAME);

        $fields = $document->getFields();

        $changedFieldValue = $fields['text_t'];

        if ($changedFieldValue != $this->entity->getText()) {
            throw new \RuntimeException(sprintf('updated entity with id %s was not updated in solr', $entityId));
        }
    }

    /**
     * @When /^I delete the entity$/
     */
    public function iDeleteTheEntity()
    {
        $this->sunClient->removeDocument($this->entity);
    }

    /**
     * @Then /^I should not find the entity in Solr$/
     */
    public function iShouldNotFindTheEntityInSolr()
    {
        $client = $this->getSolrClient();
        $entityId = $this->entity->getId();

        $query = $client->createSelect();
        $query->setQuery(sprintf('id:%s', $entityId));
        $resultset = $client->select($query);

        if ($resultset->getNumFound() > 0) {
            throw new \RuntimeException(sprintf('document with id %s should not found in the index', $entityId));
        }
    }

} 