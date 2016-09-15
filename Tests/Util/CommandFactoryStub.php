<?php

namespace ZQ\SunSearchBundle\Tests\Util;

use ZQ\SunSearchBundle\Doctrine\Annotation\AnnotationReader;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\CommandFactory;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\MapAllFieldsCommand;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\MapIdentifierCommand;
use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformationFactory;

/**
 * Class CommandFactoryStub
 */
class CommandFactoryStub
{
    /**
     *
     * @return \ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\CommandFactory
     */
    public static function getFactoryWithAllMappingCommand()
    {
        $reader = new AnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());

        $commandFactory = new CommandFactory();
        $commandFactory->add(new MapAllFieldsCommand(new MetaInformationFactory($reader)), 'all');
        $commandFactory->add(new MapIdentifierCommand(), 'identifier');

        return $commandFactory;
    }
}

