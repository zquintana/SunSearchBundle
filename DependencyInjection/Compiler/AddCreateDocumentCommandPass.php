<?php

namespace ZQ\SunSearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddCreateDocumentCommandPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->findTaggedServiceIds('sunsearch.document.command');

        $factory = $container->getDefinition('sunsearch.mapping.factory');

        foreach ($definitions as $service => $definition) {
            $factory->addMethodCall(
                'add',
                array(
                    new Reference($service),
                    $definition[0]['command']
                )
            );
        }
    }
}
