<?php

namespace ZQ\SunSearchBundle\Tests;

/**
 * Class SolrResponseFake
 */
class SolrResponseFake
{
    private $response = array();

    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}

