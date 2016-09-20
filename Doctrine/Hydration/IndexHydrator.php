<?php

namespace ZQ\SunSearchBundle\Doctrine\Hydration;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;

/**
 * Hydrates blank Entity from Document
 */
class IndexHydrator implements HydratorInterface
{
    /**
     * @var HydratorInterface
     */
    private $valueHydrator;

    /**
     * @param HydratorInterface $valueHydrator
     */
    public function __construct(HydratorInterface $valueHydrator)
    {
        $this->valueHydrator = $valueHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($document, MetaInformationInterface $metaInformation = null)
    {
        if ($metaInformation->isDoctrineEntity() === false) {
            throw new \RuntimeException(sprintf('Please check your config. Given entity is not a Doctrine entity, but Doctrine hydration is enabled. Use setHydrationMode(HydrationModes::HYDRATE_DOCTRINE) to fix this.'));
        }

        $sourceTargetEntity = $metaInformation->getEntity();
        $targetEntity = clone $sourceTargetEntity;

        $metaInformation->setEntity($targetEntity);

        return $this->valueHydrator->hydrate($document, $metaInformation);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'index';
    }
} 