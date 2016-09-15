<?php

namespace ZQ\SunSearchBundle\Logging;

/**
 * Interface SolrLogger
 */
interface SolrLoggerInterface
{
    /**
     * Called when the request is started
     *
     * @param string $request
     *
     * @return mixed
     */
    public function startRequest($request);

    /**
     * Called when the request has ended
     *
     * @return mixed
     */
    public function stopRequest();
}