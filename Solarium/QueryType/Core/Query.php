<?php

namespace ZQ\SunSearchBundle\Solarium\QueryType\Core;

use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Class Query
 */
class Query extends BaseQuery
{
    const CREATE_ACTION = 'CREATE';

    const STATUS_ACTION = 'STATUS';

    const TYPE = 'admin_core';

    /**
     * @var array
     */
    protected $options = [
        'resultclass' => Result::class,
        'handler'     => 'admin/cores',
        'omitheader'  => true,
    ];


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseParser()
    {
        return;
    }

    /**
     * @param string $core
     * @param string $configSet
     *
     * @return $this
     */
    public function createWithConfigSet($core, $configSet)
    {
        $this->setOption('args', [
            'name'      => $core,
            'configSet' => $configSet,
            'action'    => static::CREATE_ACTION,
        ]);

        return $this;
    }

    /**
     * @param string $core
     *
     * @return $this
     */
    public function status($core)
    {
        $this->setOption('args', [
            'core'   => $core,
            'action' => static::STATUS_ACTION,
        ]);

        return $this;
    }
}
