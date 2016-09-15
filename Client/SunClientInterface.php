<?php

namespace ZQ\SunSearchBundle\Client;

use ZQ\SunSearchBundle\Query\AbstractQuery;
use ZQ\SunSearchBundle\Repository\Repository;

/**
 * Interface SunClientInterface
 */
interface SunClientInterface
{

    /**
     * @param object $entity
     */
    public function removeDocument($entity);

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function addDocument($entity);

    /**
     * @param AbstractQuery $query
     *
     * @return array of found documents
     */
    public function query(AbstractQuery $query);

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function updateDocument($entity);

    /**
     * @param string $entityAlias
     *
     * @return Repository
     *
     * @throws \RuntimeException if repository of the given $entityAlias does not extend ZQ\SunSearchBundle\Repository\Repository
     */
    public function getRepository($entityAlias);

    /**
     * @param array  $doctrineChangeSet
     * @param object $entity
     *
     * @return array
     */
    public function computeChangeSet(array $doctrineChangeSet, $entity);
}