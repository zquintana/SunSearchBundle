<?php

namespace ZQ\SunSearchBundle\Tests\Integration\Bootstrap;

use ZQ\SunSearchBundle\Client\SunSunClient;
use ZQ\SunSearchBundle\Tests\Doctrine\Mapper\EntityCore0;
use ZQ\SunSearchBundle\Tests\Doctrine\Mapper\EntityCore1;
use ZQ\SunSearchBundle\Tests\Util\EntityIdentifier;

/**
 * Class MulticoreFeatureContext
 */
class MulticoreFeatureContext extends FeatureContext
{
    /**
     * @var SunSunClient
     */
    private $sunClient;

    /**
     * @var array
     */
    private $entities = array();


    /**
     * MulticoreFeatureContext constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->sunClient = $this->getSunInstance();
        $this->entities = array(
            'core0' => new EntityCore0(),
            'core1' => new EntityCore1()
        );
    }

    /**
     * @Given /^I have a Doctrine entity for "([^"]*)"$/
     */
    public function iHaveADoctrineEntity($core)
    {
        $entity = $this->entities[$core];

        $entity->setId(EntityIdentifier::generate());
        $entity->setText('a Text');

        $this->entities[$core] = $entity;
    }

    /**
     * @When /^I add these entities to Solr$/
     */
    public function iAddThisEntityToSolr()
    {
        foreach ($this->entities as $entity) {
            $this->sunClient->addDocument($entity);
        }
    }

    /**
     * @When /^both entities should be in different cores$/
     */
    public function bothEntitiesShouldBeInDifferentCores()
    {
        /* @var EntityCore0 $core0Entity */
        $core0Entity = $this->entities['core0'];

        /* @var EntityCore1 $core1Entity */
        $core1Entity = $this->entities['core1'];

        // check if core0 contains only one entity
        $document1 = $this->findDocumentById($core0Entity->getId(), 'entitycore0', 'core0');
        $document2 = $this->findDocumentById($core1Entity->getId(), 'entitycore1', 'core0');

        if ($document1 === null) {
            throw new \Exception('entity "entitycore0" should be indexed to core0');
        }

        if ($document2 !== null) {
            throw new \Exception('entity "entitycore1" should not be indexed to core0');
        }

        // check if core1 contains only one entity
        $document1 = $this->findDocumentById($core0Entity->getId(), 'entitycore0', 'core1');
        $document2 = $this->findDocumentById($core1Entity->getId(), 'entitycore1', 'core1');

        if ($document1 !== null) {
            throw new \Exception('entity "entitycore0" not should be indexed to core1');
        }

        if ($document2 === null) {
            throw new \Exception('entity "entitycore1" should be indexed to core0');
        }
    }

}