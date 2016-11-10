<?php

namespace ZQ\SunSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use ZQ\SunSearchBundle\Model\Core;

/**
 * Class CoresLoadedEvent
 */
class CoresLoadedEvent extends Event
{
    const NAME = 'sunsearch.cores_loaded';


    /**
     * @var Core[]
     */
    private $cores;


    /**
     * CoresLoadedEvent constructor.
     *
     * @param array $cores
     */
    public function __construct(array $cores = [])
    {
        $this->cores = $cores;
    }

    /**
     * @return \ZQ\SunSearchBundle\Model\Core[]
     */
    public function getCores()
    {
        return $this->cores;
    }
}
