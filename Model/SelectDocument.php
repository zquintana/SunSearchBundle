<?php

namespace ZQ\SunSearchBundle\Model;

use Solarium\QueryType\Select\Result\Document;

/**
 * Class SelectDocument
 */
class SelectDocument extends Document
{
    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $type;


    /**
     * SelectDocument constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        parent::__construct($fields);

        $this->parseDocId();
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Parses doc id into type and id
     */
    protected function parseDocId()
    {
        $aDoc = explode('_', $this['id']);

        $this->entityId = intval(array_pop($aDoc));
        $this->type     = implode('_', $aDoc);
    }

}
