<?php

namespace ZQ\SunSearchBundle\Solarium\QueryType\Core;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Class RequestBuilder
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_GET);
        $request->addParams($query->getOption('args'));

        return $request;
    }
}
