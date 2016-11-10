<?php
namespace ZQ\SunSearchBundle\Event;

use Solarium\Client;
use Symfony\Component\EventDispatcher\Event as BaseEvent;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;

/**
 * Class Event
 */
class Event extends BaseEvent
{
    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var MetaInformationInterface
     */
    private $metaInformation = null;

    /**
     * something like 'update-solr-document'
     *
     * @var string
     */
    private $solrAction = '';

    /**
     * @var Event
     */
    private $sourceEvent;

    /**
     * @param Client                   $client
     * @param MetaInformationInterface $metaInformation
     * @param string                   $solrAction
     * @param Event                    $sourceEvent
     */
    public function __construct(
        Client $client = null,
        MetaInformationInterface $metaInformation = null,
        $solrAction = '',
        Event $sourceEvent = null
    ) {
        $this->client = $client;
        $this->metaInformation = $metaInformation;
        $this->solrAction = $solrAction;
        $this->sourceEvent = $sourceEvent;
    }

    /**
     * @return MetaInformationInterface
     */
    public function getMetaInformation()
    {
        return $this->metaInformation;
    }

    /**
     * @return string
     */
    public function getSolrAction()
    {
        return $this->solrAction;
    }

    /**
     * @return Event
     */
    public function getSourceEvent()
    {
        return $this->sourceEvent;
    }

    /**
     * @return bool
     */
    public function hasSourceEvent()
    {
        return $this->sourceEvent !== null;
    }
}
