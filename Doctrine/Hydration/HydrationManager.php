<?php

namespace ZQ\SunSearchBundle\Doctrine\Hydration;

/**
 * Class HydrationManager
 */
class HydrationManager
{
    /**
     * @var HydratorInterface[]
     */
    protected $hydrators = [];


    /**
     * @param HydratorInterface $hydrator
     *
     * @return $this
     */
    public function register(HydratorInterface $hydrator)
    {
        $this->hydrators[$hydrator->getName()] = $hydrator;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return HydratorInterface
     */
    public function get($name)
    {
        return $this->hydrators[$name];
    }

    /**
     * @return HydratorInterface[]
     */
    public function all()
    {
        return $this->hydrators;
    }
}
