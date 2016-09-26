<?php

namespace ZQ\SunSearchBundle\Doctrine\Mapper;

use Solarium\QueryType\Update\Query\Document\Document;
use ZQ\SunSearchBundle\Doctrine\Hydration\HydrationManager;
use ZQ\SunSearchBundle\Doctrine\Hydration\HydrationModes;
use ZQ\SunSearchBundle\Doctrine\Hydration\HydratorInterface;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\AbstractDocumentCommand;

/**
 * Class EntityMapper
 */
class EntityMapper
{
    /**
     * @var AbstractDocumentCommand
     */
    private $mappingCommand = null;

    /**
     * @var HydrationManager
     */
    private $hydrationManager;

    /**
     * @var string
     */
    private $hydrationMode = '';

    /**
     * @var MetaInformationFactory
     */
    private $metaInformationFactory;


    /**
     * @param HydrationManager       $hydrationManager
     * @param MetaInformationFactory $metaInformationFactory
     */
    public function __construct(
        HydrationManager $hydrationManager,
        MetaInformationFactory $metaInformationFactory
    ) {
        $this->hydrationManager = $hydrationManager;
        $this->metaInformationFactory = $metaInformationFactory;
        $this->hydrationMode = HydrationModes::HYDRATE_DOCTRINE;
    }

    /**
     * @param AbstractDocumentCommand $command
     */
    public function setMappingCommand(AbstractDocumentCommand $command)
    {
        $this->mappingCommand = $command;
    }

    /**
     * @param MetaInformationInterface $meta
     *
     * @return Document
     */
    public function toDocument(MetaInformationInterface $meta)
    {
        if ($this->mappingCommand instanceof AbstractDocumentCommand) {
            return $this->mappingCommand->createDocument($meta);
        }

        return null;
    }

    /**
     * @param \ArrayAccess $document
     * @param object       $sourceTargetEntity
     *
     * @return object|null
     *
     * @throws \InvalidArgumentException if $sourceTargetEntity is null
     */
    public function toEntity(\ArrayAccess $document, $sourceTargetEntity = null)
    {
        $metaInformation = null;
        if ($sourceTargetEntity) {
            $metaInformation = $this->metaInformationFactory->loadInformation($sourceTargetEntity);
        }

        return $this->hydrationManager->get($this->hydrationMode)->hydrate($document, $metaInformation);
    }

    /**
     * @param string $mode
     *
     * @return EntityMapper $this
     */
    public function setHydrationMode($mode)
    {
        $this->hydrationMode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getHydrationMode()
    {
        return $this->hydrationMode;
    }
}
