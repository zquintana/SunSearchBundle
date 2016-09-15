<?php

namespace ZQ\SunSearchBundle\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class VirtualField
 *
 * @Annotation
 * @Target("METHOD")
 */
class VirtualField extends Field
{
    /**
     * @var string
     */
    public $alias;


    /**
     * {@inheritdoc}
     */
    public function getNameWithAlias()
    {
        if (!empty($this->alias)) {
            return $this->normalizeName($this->alias) . $this->getTypeSuffix($this->type);
        } else {
            return $this->normalizeName($this->name) . $this->getTypeSuffix($this->type);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeName($name)
    {
        if (strpos(strtolower($name), 'get') === 0) {
            $name   = lcfirst(substr($name, 3, strlen($name)));
        }

        return parent::normalizeName($name);
    }
}
