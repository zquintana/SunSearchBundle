<?php

namespace ZQ\SunSearchBundle\Solarium\Plugin;

use ZQ\SunSearchBundle\Solarium\QueryType\Core\Query as CoreQuery;
use Solarium\Core\Plugin\AbstractPlugin;

/**
 * Class AddQueryTypesPlugin
 */
class AddQueryTypesPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    protected function initPluginType()
    {
        $this->client->registerQueryType(CoreQuery::TYPE, CoreQuery::class);
    }
}
