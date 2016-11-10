<?php

namespace ZQ\SunSearchBundle\Solarium\QueryType\Entity;

/**
 * Class FindByIdentifierQuery
 */
class FindByIdentifierQuery extends AbstractQuery
{
    /**
     * @var string
     */
    private $documentKey;

    /**
     * @param string $documentKey
     */
    public function setDocumentKey($documentKey)
    {
        $this->documentKey = $documentKey;
    }

    /**
     * @return string
     *
     * @throws \RuntimeException when id or document_name is null
     */
    public function getQuery()
    {
        $this->setQuery($this->prepareQuery());

        return parent::getQuery();
    }

    /**
     * @return string
     */
    public function prepareQuery()
    {
        $idField = $this->documentKey;

        if ($idField == null) {
            throw new \RuntimeException('id should not be null');
        }

        return sprintf('id:%s', $idField);
    }
}
