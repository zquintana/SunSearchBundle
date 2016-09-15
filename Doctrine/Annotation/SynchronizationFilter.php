<?php

namespace ZQ\SunSearchBundle\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class SynchronizationFilter extends Annotation
{
    public $callback = '';
}