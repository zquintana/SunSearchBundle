<?php

namespace ZQ\SunSearchBundle\Model;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Core[]
     */
    private $cores;

    /**
     * Core scoped endpoint
     *
     * @var Endpoint[]
     */
    private $endpoints = [];


    /**
     * CoreManager constructor.
     *
     * @param Client                   $client
     * @param EventDispatcherInterface $dispatcher
     * @param array                    $cores
     */
    public function __construct(
        Client $client,
        EventDispatcherInterface $dispatcher,
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
        if (empty($name)) {
            $keys = array_keys($this->cores);

            return $this->cores[$keys[0]];
        }

        if (!isset($this->cores[$name])) {
            throw new CoreManagerException(sprintf('Unknown core "%s".', $name));
        }

        return $this->cores[$name];
    }

    /**
     * @param string|Core $name
     *
     * @return \Solarium\Core\Client\Endpoint
     */
    public function getEndpoint($name)
    {
        $core = $name instanceof Core ? $name : $this->getCore($name);
        $name = $core->getName();

        if (!isset($this->endpoints[$name])) {
            $endpoint = clone $this->client->getEndpoint($core->getConnection());
            $endpoint->setCore($core->getCoreName());
            $this->endpoints[$name] = $endpoint;
        }

        return $this->endpoints[$name];
    }

    /**
     * @return EventDispatcherInterface
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
