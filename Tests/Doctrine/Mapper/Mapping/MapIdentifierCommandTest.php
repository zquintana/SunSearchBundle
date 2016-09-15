<?php

namespace ZQ\SunSearchBundle\Tests\Doctrine\Mapper\Mapping;

use ZQ\SunSearchBundle\Doctrine\Annotation\AnnotationReader;
use ZQ\SunSearchBundle\Doctrine\Mapper\Command\CreateDeletedDocumentCommand;
use ZQ\SunSearchBundle\Doctrine\Mapper\Mapping\MapIdentifierCommand;
use ZQ\SunSearchBundle\Tests\Util\MetaTestInformationFactory;

/**
 * @group mappingcommands
 */
class MapIdentifierCommandTest extends SolrDocumentTest
{

    public function testCreateDocument_DocumentHasOnlyIdAndNameField()
    {
        $command = new MapIdentifierCommand();

        $document = $command->createDocument(MetaTestInformationFactory::getMetaInformation());

        $this->assertEquals(1, $document->count(), 'fieldcount is two');
        $this->assertEquals('validtestentity_2', $document->id, 'id is 2');

    }

}

