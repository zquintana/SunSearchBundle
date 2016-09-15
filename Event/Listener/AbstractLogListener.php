<?php

namespace ZQ\SunSearchBundle\Event\Listener;

use Psr\Log\LoggerInterface;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;

/**
 * Class AbstractLogListener
 */
abstract class AbstractLogListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger = null;


    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MetaInformationInterface $metaInformation
     *
     * @return string
     */
    protected function createDocumentNameWithId(MetaInformationInterface $metaInformation)
    {
        return $metaInformation->getDocumentName() . ':' . $metaInformation->getEntityId();
    }

    /**
     * @param MetaInformationInterface $metaInformation
     *
     * @return string
     */
    protected function createFieldList(MetaInformationInterface $metaInformation)
    {
        return implode(', ', $metaInformation->getFields());
    }
}
