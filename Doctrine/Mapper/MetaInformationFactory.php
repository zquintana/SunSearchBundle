<?php

namespace ZQ\SunSearchBundle\Doctrine\Mapper;

use ZQ\SunSearchBundle\Doctrine\Annotation\AnnotationReader;
use ZQ\SunSearchBundle\Doctrine\ClassnameResolver\ClassnameResolver;

/**
 * instantiates a new MetaInformation object by a given entity
 */
class MetaInformationFactory
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader = null;

    /**
     * @var ClassnameResolver
     */
    private $classnameResolver = null;

    /**
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->annotationReader = $reader;
    }

    /**
     * @param ClassnameResolver $classnameResolver
     */
    public function setClassnameResolver(ClassnameResolver $classnameResolver)
    {
        $this->classnameResolver = $classnameResolver;
    }

    /**
     * @param object $entity
     *
     * @return MetaInformation
     *
     * @throws \RuntimeException if no declaration for document found in $entity
     */
    public function loadInformation($entity)
    {

        $className = $this->getClass($entity);

        if (!is_object($entity)) {
            $reflectionClass = new \ReflectionClass($className);
            if (!$reflectionClass->isInstantiable()) {
                throw new \RuntimeException(sprintf('cannot instantiate entity %s', $className));
            }
            $entity = $reflectionClass->newInstanceWithoutConstructor();
        }

        if (!$this->annotationReader->hasDocumentDeclaration($entity)) {
            throw new \RuntimeException(sprintf('no declaration for document found in entity %s', $className));
        }

        $metaInformation = new MetaInformation();
        $metaInformation->setEntity($entity);
        $metaInformation->setClassName($className);
        $metaInformation->setDocumentName($this->getDocumentName($className));
        $metaInformation->setFieldMapping($this->annotationReader->getFieldMapping($entity));
        $metaInformation->setFields($this->annotationReader->getFields($entity));
        $metaInformation->setRepository($this->annotationReader->getRepository($entity));
        $metaInformation->setIdentifier($this->annotationReader->getIdentifier($entity));
        $metaInformation->setBoost($this->annotationReader->getEntityBoost($entity));
        $metaInformation->setSynchronizationCallback($this->annotationReader->getSynchronizationCallback($entity));
        $metaInformation->setIndex($this->annotationReader->getDocumentIndex($entity));
        $metaInformation->setIsDoctrineEntity($this->annotationReader->isDoctrineEntity($entity));

        return $metaInformation;
    }

    /**
     * @param object $entity
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function getClass($entity)
    {
        if (is_object($entity)) {
            return get_class($entity);
        }

        if (class_exists($entity)) {
            return $entity;
        }

        $realClassName = $this->classnameResolver->resolveFullQualifiedClassname($entity);

        return $realClassName;
    }

    /**
     * @param string $fullClassName
     *
     * @return string
     */
    private function getDocumentName($fullClassName)
    {
        $className = substr($fullClassName, (strrpos($fullClassName, '\\') + 1));

        return strtolower($className);
    }
}
