<?php

namespace ZQ\SunSearchBundle\Model;

use Solarium\Core\Client\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use ZQ\SunSearchBundle\Event\CoresLoadedEvent;
use ZQ\SunSearchBundle\Exception\CoreManagerException;

/**
 * Class CoreManager
 */
class CoreManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Core[]
     */
    private $cores;


    /**
     * CoreManager constructor.
     *
     * @param Client          $client
     * @param EventDispatcher $dispatcher
     * @param array           $cores
     */
    public function __construct(
        Client $client,
        EventDispatcher $dispatcher,
        array $cores = []
    ) {
        $this->client     = $client;
        $this->dispatcher = $dispatcher;
        $this->setCores($cores);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Core[]
     */
    public function getCores()
    {
        return $this->cores;
    }

    /**
     * @param Core $core
     *
     * @return $this
     */
    public function setCore(Core $core)
    {
        $this->cores[$core->getName()] = $core;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Core
     *
     * @throws CoreManagerException
     */
    public function getCore($name)
    {
        if (!isset($this->cores[$name])) {
            throw new CoreManagerException(sprintf('Unknown core "%s".', $name));
        }

        return $this->cores[$name];
    }

    /**
     * @param string $name
     *
     * @return \Solarium\Core\Client\Endpoint
     */
    public function getEndpoint($name)
    {
        $core = $this->getCore($name);

        return $this->client->getEndpoint($core->getConnection());
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param array $cores
     *
     * @return $this
     */
    private function setCores(array $cores)
    {
        foreach ($cores as $core => $options) {
            $this->setCore(new Core($core, $options));
        }

        $this->dispatcher->dispatch(CoresLoadedEvent::NAME, new CoresLoadedEvent($this->cores));

        return $this;
    }
}
