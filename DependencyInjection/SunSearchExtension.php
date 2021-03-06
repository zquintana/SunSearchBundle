<?php

namespace ZQ\SunSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class SunSearchExtension
 */
class SunSearchExtension extends Extension
{

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('hydrators.xml');
        $loader->load('event_listener.xml');
        $loader->load('log_listener.xml');
        $loader->load('plugins.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->setupClients($config, $container);

        $container->setParameter('sunsearch.auto_index', $config['auto_index']);

        $this->setupDoctrineListener($config, $container);
        $this->setupDoctrineConfiguration($config, $container);
        $this->setupHydrationManager($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function setupHydrationManager(ContainerBuilder $container)
    {
        $manager = $container->getDefinition('sunsearch.hydration_manager');

        foreach ($container->findTaggedServiceIds('sunsearch.hydrator') as $id => $tags) {
            $manager->addMethodCall('register', [ new Reference($id) ]);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function setupClients(array $config, ContainerBuilder $container)
    {
        $endpoints = $config['connections'];

        if (!isset($endpoints['default'])) {
            throw new Exception('SunSearch connections config expects at least one connection of name "default".');
        }

        $builderDefinition = $container->getDefinition('sunsearch.client.adapter.builder');
        $builderDefinition->replaceArgument(0, $endpoints);

        $mgrDef = $container->getDefinition('sunsearch.core_manager');
        $mgrDef->addArgument($config['cores']);
    }

    /**
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function setupDoctrineConfiguration(array $config, ContainerBuilder $container)
    {
        if ($this->isOrmConfigured($container)) {
            $entityManagers = $container->getParameter('doctrine.entity_managers');

            $entityManagersNames = array_keys($entityManagers);
            foreach ($entityManagersNames as $entityManager) {
                $container->getDefinition('sunsearch.doctrine.classnameresolver.known_entity_namespaces')->addMethodCall(
                    'addEntityNamespaces',
                    array(new Reference(sprintf('doctrine.orm.%s_configuration', $entityManager)))
                );
            }
        }

        if ($this->isODMConfigured($container)) {
            $documentManagers = $container->getParameter('doctrine_mongodb.odm.document_managers');

            $documentManagersNames = array_keys($documentManagers);
            foreach ($documentManagersNames as $documentManager) {
                $container->getDefinition('sunsearch.doctrine.classnameresolver.known_entity_namespaces')->addMethodCall(
                    'addDocumentNamespaces',
                    array(new Reference(sprintf('doctrine_mongodb.odm.%s_configuration', $documentManager)))
                );
            }
        }

        $container->getDefinition('sunsearch.meta.information.factory')->addMethodCall(
            'setClassnameResolver',
            array(new Reference('sunsearch.doctrine.classnameresolver'))
        );
    }

    /**
     * doctrine_orm and doctrine_mongoDB can't be used together. mongo_db wins when it is configured.
     *
     * listener-methods expecting different types of events
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function setupDoctrineListener(array $config, ContainerBuilder $container)
    {
        $autoIndexing = $container->getParameter('sunsearch.auto_index');

        if ($autoIndexing == false) {
            return;
        }

        if ($this->isODMConfigured($container)) {
            $container->getDefinition('sunsearch.document.odm.subscriber')->addTag('doctrine_mongodb.odm.event_subscriber');
        }

        if ($this->isOrmConfigured($container)) {
            $container->getDefinition('sunsearch.document.orm.subscriber')->addTag('doctrine.event_subscriber');
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return boolean
     */
    private function isODMConfigured(ContainerBuilder $container)
    {
        return $container->hasParameter('doctrine_mongodb.odm.document_managers');
    }

    /**
     * @param ContainerBuilder $container
     * @return boolean
     */
    private function isOrmConfigured(ContainerBuilder $container)
    {
        return $container->hasParameter('doctrine.entity_managers');
    }
}
