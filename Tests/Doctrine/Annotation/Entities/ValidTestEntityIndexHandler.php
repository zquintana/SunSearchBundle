<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Solr;

/**
 *
 * @Sun\Document(indexHandler="indexHandler")
 */
class ValidTestEntityIndexHandler
{

    /**
     * @Sun\Id
     */
    private $id;

    /**
     *
     * @Sun\Field
     */
    private $title;

    /**
     * @return string
     */
    public function indexHandler()
    {
        return 'my_core';
    }
}

