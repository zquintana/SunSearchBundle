<?php

namespace ZQ\SunSearchBundle\Tests\Solr\Doctrine;

use ZQ\SunSearchBundle\Doctrine\ClassnameResolver\ClassnameResolver;

/**
 * @group resolver
 */
class ClassnameResolverTest extends \PHPUnit_Framework_TestCase
{
    const ENTITY_NAMESPACE = 'ZQ\SunSearchBundle\Tests\Doctrine\Mapper';
    const UNKNOW_ENTITY_NAMESPACE = 'FS\Unknown';

    private $knownAliases;

    public function setUp()
    {
        $this->knownAliases = $this->getMock('ZQ\SunSearchBundle\Doctrine\ClassnameResolver\KnownNamespaceAliases', array(), array(), '', false);
    }

    /**
     * @test
     */
    public function resolveClassnameOfCommonEntity()
    {
        $resolver = $this->getResolverWithKnowNamespace(self::ENTITY_NAMESPACE);

        $expectedClass = 'ZQ\SunSearchBundle\Tests\Doctrine\Mapper\ValidTestEntity';

        $this->assertEquals($expectedClass, $resolver->resolveFullQualifiedClassname('FSTest:ValidTestEntity'));
    }

    /**
     * @test
     * @expectedException \ZQ\SunSearchBundle\Doctrine\ClassnameResolver\ClassnameResolverException
     */
    public function cantResolveClassnameFromUnknowClassWithValidNamespace()
    {
        $resolver = $this->getResolverWithOrmAndOdmConfigBothHasEntity(self::ENTITY_NAMESPACE);

        $resolver->resolveFullQualifiedClassname('FSTest:UnknownEntity');
    }

    /**
     * @test
     * @expectedException \ZQ\SunSearchBundle\Doctrine\ClassnameResolver\ClassnameResolverException
     */
    public function cantResolveClassnameIfEntityNamespaceIsUnknown()
    {
        $resolver = $this->getResolverWithOrmConfigPassedInvalidNamespace(self::UNKNOW_ENTITY_NAMESPACE);

        $resolver->resolveFullQualifiedClassname('FStest:entity');
    }

    /**
     * both has a namespace
     *
     * @param string $knownNamespace
     * @return ClassnameResolver
     */
    private function getResolverWithOrmAndOdmConfigBothHasEntity($knownNamespace)
    {
        $this->knownAliases->expects($this->once())
            ->method('isKnownNamespaceAlias')
            ->will($this->returnValue(true));

        $this->knownAliases->expects($this->once())
            ->method('getFullyQualifiedNamespace')
            ->will($this->returnValue($knownNamespace));

        $resolver = new ClassnameResolver($this->knownAliases);

        return $resolver;
    }

    private function getResolverWithOrmConfigPassedInvalidNamespace($knownNamespace)
    {
        $this->knownAliases->expects($this->once())
            ->method('isKnownNamespaceAlias')
            ->will($this->returnValue(false));

        $this->knownAliases->expects($this->once())
            ->method('getAllNamespaceAliases')
            ->will($this->returnValue(array('FSTest')));

        $resolver = new ClassnameResolver($this->knownAliases);

        return $resolver;
    }

    private function getResolverWithKnowNamespace($knownNamespace)
    {
        $this->knownAliases->expects($this->once())
            ->method('isKnownNamespaceAlias')
            ->will($this->returnValue(true));

        $this->knownAliases->expects($this->once())
            ->method('getFullyQualifiedNamespace')
            ->will($this->returnValue($knownNamespace));

        $resolver = new ClassnameResolver($this->knownAliases);

        return $resolver;
    }
}
 