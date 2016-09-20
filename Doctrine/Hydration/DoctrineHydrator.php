<?php

namespace ZQ\SunSearchBundle\Doctrine\Hydration;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * A doctrine-hydrator finds the entity for a given solr-document. This entity is updated with the solr-document values.
 *
 * The hydration is necessary because fields, which are not declared as solr-field, will not populate in the result.
 */
class DoctrineHydrator implements HydratorInterface
{

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var HydratorInterface
     */
    private $indexHydrator;

    /**
     * @param RegistryInterface $doctrine
     * @param HydratorInterface $indexHydrator
     */
    public function __construct(RegistryInterface $doctrine, HydratorInterface $indexHydrator)
    {
        $this->doctrine = $doctrine;
        $this->indexHydrator = $indexHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($document, MetaInformationInterface $metaInformation = null)
    {
        $hydratedDocument = $this->indexHydrator->hydrate($document, $metaInformation);
        $metaInformation->setEntity($hydratedDocument);

        $entityId = $metaInformation->getEntityId();
        $doctrineEntity = $this->doctrine
            ->getManager()
            ->getRepository($metaInformation->getClassName())
            ->find($entityId);

        return $doctrineEntity;
    }

    /**
     * @param array                    $entities
     * @param MetaInformationInterface $metaInformation
     *
     * @return array
     */
    public function hydrateEntities(array $entities, MetaInformationInterface $metaInformation)
    {
        $ids    = [];
        foreach ($entities as $entity) {
            $metaInformation->setEntity($entity);
            array_push($ids, $metaInformation->getEntityId());
        }

        $finderMethod = $metaInformation->getFinderMethod();
        $repo         = $this->doctrine->getManager()->getRepository($metaInformation->getClassName());
        if ($finderMethod && method_exists($repo, $finderMethod)) {
            return $repo->{$finderMethod}($ids);
        } else {
            return $repo->findBy(['id' => $ids]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine';
    }
}
