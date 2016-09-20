<?php

namespace ZQ\SunSearchBundle\Doctrine\Hydration;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationInterface;
use ZQ\SunSearchBundle\Doctrine\Annotation\Field;

/**
 * Class DoctrineValueHydrator
 */
class DoctrineValueHydrator extends ValueHydrator
{
    /**
     * {@inheritdoc}
     */
    public function mapValue($fieldName, $value, MetaInformationInterface $metaInformation = null)
    {
        if (is_array($value)) {
            return false;
        }

        if ($metaInformation->getField($fieldName) && $metaInformation->getField($fieldName)->getter) {
            return false;
        }

        $fieldSuffix = $this->removePrefixedKeyFieldName($fieldName);
        if ($fieldSuffix === false) {
            return false;
        }

        if (array_key_exists($fieldSuffix, Field::getComplexFieldMapping())) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine_value';
    }
}