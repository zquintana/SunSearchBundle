<?php
namespace ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities;

use ZQ\SunSearchBundle\Doctrine\Annotation as Solr;

/**
 *
 * @Solr\Document(repository="ZQ\SunSearchBundle\Tests\Doctrine\Annotation\Entities\InvalidEntityRepository")
 *
 */
class EntityWithInvalidRepository
{

}
