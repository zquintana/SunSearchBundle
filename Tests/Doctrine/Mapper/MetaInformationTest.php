<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Mapper;

use ZQ\SunSearchBundle\Doctrine\Mapper\MetaInformation;

/**
 *
 * @group mapper
 */
class MetaInformationTest extends \PHPUnit_Framework_TestCase
{
    private function createFieldObject($name, $value)
    {
        $value = new \stdClass();
        $value->name = $name;
        $value->value = $value;

        return $value;
    }

    public function testHasCallback_CallbackSet()
    {
        $information = new MetaInformation();
        $information->setSynchronizationCallback('function');

        $this->assertTrue($information->hasSynchronizationFilter(), 'has callback');
    }

    public function testHasCallback_NoCallbackSet()
    {
        $information = new MetaInformation();

        $this->assertFalse($information->hasSynchronizationFilter(), 'has no callback');
    }
}

