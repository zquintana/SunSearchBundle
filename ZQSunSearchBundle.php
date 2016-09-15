<?php

namespace ZQ\SunSearchBundle\SolrBundle;

use ZQ\SunSearchBundle\DependencyInjection\Compiler\AddCreateDocumentCommandPass;
use ZQ\SunSearchBundle\DependencyInjection\Compiler\AddSolariumPluginsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ZQSunSearchBundle
 */
class ZQSunSearchBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new AddCreateDocumentCommandPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION)
            ->addCompilerPass(new AddSolariumPluginsPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION)
        ;
    }
}