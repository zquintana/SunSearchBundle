<?php

namespace ZQ\SunSearchBundle\Tests\Integration\Bootstrap;

use Behat\Behat\Context\Context;
use Doctrine\ORM\Configuration;
use ZQ\SunSearchBundle\Client\Solarium\SolariumClientBuilder;
use ZQ\SunSearchBundle\Client\SunSunClient;
use ZQ\SunSearchBundle\Doctrine\Annotation\AnnotationReader;
use ZQ\SunSearchBundle\Doctrine\ClassnameResolver\ClassnameResolver;
use ZQ\SunSearchBundle\Doctrine\ClassnameResolver\KnownNamespaceAliases;
use ZQ\SunSearchBundle\Doctrine\Hydration\DoctrineHydrator;
use ZQ\SunSearchBundle\Doctrine\Hydration\IndexHydrator;
use ZQ\SunSearchBundle\Doctrine\Hydration\ValueHydrator;
use ZQ\SunSearchBundle\Doctrine\Mapper\EntityMapper;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\CommandFactory;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\MapAllFieldsCommand;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\MapIdentifierCommand;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationFactory;
use ZQ\SunSearchBundle\Tests\Integration\DoctrineRegistryFake;
use ZQ\SunSearchBundle\Tests\Integration\EventDispatcherFake;
use Solarium\Client;

class SolrSetupFeatureContext implements Context
{
    /**
     * @var EventDispatcherFake
     */
    private $eventDispatcher;

    /**
     * @var Client
     */
    private $solrClient;

    public function __construct()
    {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        } else {
            require_once 'vendor/autoload.php';
        }

        $this->eventDispatcher = new EventDispatcherFake();
    }

    /**
     * @return EventDispatcherFake
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return Client
     */
    public function getSolrClient()
    {
        return $this->solrClient;
    }

    /**
     * @return SunSunClient
     */
    public function getSunInstance()
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::registerAnnotationClasses();

        $this->solrClient = $this->setupSolrClient();
        $factory = $this->setupCommandFactory();
        $metaFactory = $this->setupMetaInformationFactory();
        $entityMapper = $this->setupEntityMapper();

        $sunSearch = new SunSunClient(
            $this->solrClient,
            $factory,
            $this->eventDispatcher,
            $metaFactory,
            $entityMapper
        );

        return $sunSearch;
    }

    /**
     * @return EntityMapper
     */
    private function setupEntityMapper()
    {
        $registry = new DoctrineRegistryFake();

        $reader = new AnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());

        $metaFactory = new MetaInformationFactory($reader);

        $entityMapper = new EntityMapper(
            new DoctrineHydrator(
                $registry,
                new ValueHydrator()
            ),
            new IndexHydrator(
                new ValueHydrator()
            ),
            $metaFactory
        );

        return $entityMapper;
    }

    /**
     * @return CommandFactory
     */
    private function setupCommandFactory()
    {
        $reader = new AnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());

        $factory = new CommandFactory();
        $factory->add(new MapAllFieldsCommand(new MetaInformationFactory($reader)), 'all');
        $factory->add(new MapIdentifierCommand(), 'identifier');

        return $factory;
    }

    /**
     * @return MetaInformationFactory
     */
    private function setupMetaInformationFactory()
    {
        $ormConfiguration = new Configuration();
        $ormConfiguration->addEntityNamespace('FSTest:ValidTestEntity', 'ZQ\SunSearchBundle\Tests\Doctrine\Mapper');
        $ormConfiguration->addEntityNamespace('FSTest:EntityCore0', 'ZQ\SunSearchBundle\Tests\Doctrine\Mapper');
        $ormConfiguration->addEntityNamespace('FSTest:EntityCore1', 'ZQ\SunSearchBundle\Tests\Doctrine\Mapper');

        $knowNamespaces = new KnownNamespaceAliases();
        $knowNamespaces->addEntityNamespaces($ormConfiguration);

        $classnameResolver = new ClassnameResolver($knowNamespaces);

        $reader = new AnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());

        $metaFactory = new MetaInformationFactory($reader);
        $metaFactory->setClassnameResolver(
            $classnameResolver
        );

        return $metaFactory;
    }

    /**
     * Solarium SunSunClient with two cores (core0, core1)
     *
     * @return Client
     */
    private function setupSolrClient()
    {
        $config = array(
            'core0' => array(
                'host' => 'localhost',
                'port' => 8983,
                'path' => '/solr/core0',
            ),
            'core1' => array(
                'host' => 'localhost',
                'port' => 8983,
                'path' => '/solr/core1',
            ),
        );

        $builder = new SolariumClientBuilder($config, $this->eventDispatcher);
        $solrClient = $builder->build();

        return $solrClient;
    }
}