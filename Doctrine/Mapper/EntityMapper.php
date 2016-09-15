<?php
namespace ZQ\SunSearchBundle\Doctrine\Mapper;

use Solarium\QueryType\Update\Query\Document\Document;
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
     * @var HydratorInterface
     */
    private $doctrineHydrator;

    /**
     * @var HydratorInterface
     */
    private $indexHydrator;

    /**
     * @var string
     */
    private $hydrationMode = '';

    /**
     * @var MetaInformationFactory
     */
    private $metaInformationFactory;


    /**
     * @param HydratorInterface      $doctrineHydrator
     * @param HydratorInterface      $indexHydrator
     * @param MetaInformationFactory $metaInformationFactory
     */
    public function __construct(
        HydratorInterface $doctrineHydrator,
        HydratorInterface $indexHydrator,
        MetaInformationFactory $metaInformationFactory
    ) {
        $this->doctrineHydrator = $doctrineHydrator;
        $this->indexHydrator = $indexHydrator;
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
    public function toEntity(\ArrayAccess $document, $sourceTargetEntity)
    {
        if (null === $sourceTargetEntity) {
            throw new \InvalidArgumentException('$sourceTargetEntity should not be null');
        }

        $metaInformation = $this->metaInformationFactory->loadInformation($sourceTargetEntity);

        if ($metaInformation->isDoctrineEntity() === false && $this->hydrationMode == HydrationModes::HYDRATE_DOCTRINE) {
            throw new \RuntimeException(sprintf('Please check your config. Given entity is not a Doctrine entity, but Doctrine hydration is enabled. Use setHydrationMode(HydrationModes::HYDRATE_DOCTRINE) to fix this.'));
        }

        $hydratedDocument = $this->indexHydrator->hydrate($document, $metaInformation);

        if ($this->hydrationMode == HydrationModes::HYDRATE_INDEX) {
            return $hydratedDocument;
        }

        $metaInformation->setEntity($hydratedDocument);

        if ($this->hydrationMode == HydrationModes::HYDRATE_DOCTRINE) {
            return $this->doctrineHydrator->hydrate($document, $metaInformation);
        }

        return null;
    }

    /**
     * @param string $mode
     */
    public function setHydrationMode($mode)
    {
        $this->hydrationMode = $mode;
    }
}
