<?php

namespace ZQ\SunSearchBundle\Logging;


/**
 * Class DebugLogger
 *
 * Logs the current request and information about this request
 */
class DebugLogger implements SolrLoggerInterface
{
    /**
     * @var float
     */
    private $start;

    /**
     * @var array
     */
    private $queries = [];

    /**
     * @var integer
     */
    private $currentQuery = 0;


    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     */
    public function startRequest($request)
    {
        $this->start = microtime(true);
        $this->queries[++$this->currentQuery] = [
            'request'       => $request,
            'executionMS'   => 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stopRequest()
    {
        $this->queries[$this->currentQuery]['executionMS']  = microtime(true) - $this->start;
    }
}