<?php

namespace ZQ\SunSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sun_search');
        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')->prototype('array')
                        ->children()
                            ->scalarNode('host')->end()
                            ->scalarNode('port')->end()
                            ->scalarNode('path')->end()
                            ->scalarNode('timeout')->end()
                            ->booleanNode('active')->defaultValue(true)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cores')
                    ->useAttributeAsKey('core')->prototype('array')
                        ->children()
                            ->scalarNode('config_set')->end()
                            ->scalarNode('connection')->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('auto_index')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}
