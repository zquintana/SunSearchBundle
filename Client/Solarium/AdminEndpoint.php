<?php

namespace ZQ\SunSearchBundle\Client\Solarium;

use Solarium\Core\Client\Endpoint;

/**
 * Class AdminEndpoint
 */
class AdminEndpoint extends Endpoint
{
    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath();
    }
}
