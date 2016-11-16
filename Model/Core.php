<?php

namespace ZQ\SunSearchBundle\Model;

use Solarium\Core\Configurable;

/**
 * Class Core
 */
class Core extends Configurable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $coreName;


    /**
     * Core constructor.
     *
     * @param string     $name
     * @param array|null $options
     */
    public function __construct($name, array $options = [])
    {
        $this->name = $name;

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        $connection = $this->getOption('connection');

        return $connection ?: 'default';
    }

    /**
     * @return string
     */
    public function getConfigSet()
    {
        return $this->getOption('config_set');
    }

    /**
     * @return string
     */
    public function getCoreName()
    {
        if (!$this->coreName) {
            $this->coreName = $this->name;
        }

        return $this->coreName;
    }

    /**
     * @param string $coreName
     *
     * @return Core
     */
    public function setCoreName($coreName)
    {
        $this->coreName = $coreName;

        return $this;
    }
}
