<?php

namespace ZQ\SunSearchBundle\Client;

/**
 * Interface Builder
 */
interface Builder
{
    /**
     * returns a implementation of a solr-client
     *
     * @return mixed
     */
    public function build();
} 