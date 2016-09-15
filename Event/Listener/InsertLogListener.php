<?php
namespace ZQ\SunSearchBundle\Event\Listener;

use ZQ\SunSearchBundle\Event\Event;

/**
 * Create a log-entry if a document was insert
 */
class InsertLogListener extends AbstractLogListener
{

    /**
     * @param Event $event
     */
    public function onSolrInsert(Event $event)
    {
        $metaInformation = $event->getMetaInformation();

        $nameWithId = $this->createDocumentNameWithId($metaInformation);
        $fieldList = $this->createFieldList($metaInformation);

        $this->logger->debug(
            sprintf('document %s with fields %s was added', $nameWithId, $fieldList)
        );
    }
}
