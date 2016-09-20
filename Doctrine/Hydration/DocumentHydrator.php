<?php

namespace ZQ\SunSearchBundle\Doctrine\Hydration;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;
use ZQ\SunSearchBundle\Model\Document;
use ZQ\SunSearchBundle\Model\SelectDocument;

/**
 * Class DocumentHydrator
 */
class DocumentHydrator implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function hydrate($document, MetaInformationInterface $metaInformation = null)
    {
        return new SelectDocument($document->getFields());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'document';
    }
}
