<?php

namespace ZQ\SunSearchBundle\Event\Listener;

use ZQ\SunSearchBundle\Event\Event;

/**
 * Create a log-entry if a document was updated
 */
class UpdateLogListener extends AbstractLogListener
{

    /**
     * @param Event $event
     */
    public function onSolrUpdate(Event $event)
    {
        $metaInformation = $event->getMetaInformation();

        $nameWithId = $this->createDocumentNameWithId($metaInformation);
        $fieldList = $this->createFieldList($metaInformation);

        $this->logger->debug(
            sprintf('document %s with fields %s was updated', $nameWithId, $fieldList)
        );
    }
}
