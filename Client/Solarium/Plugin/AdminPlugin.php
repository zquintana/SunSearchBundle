<?php

namespace ZQ\SunSearchBundle\Client\Solarium\Plugin;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Plugin\AbstractPlugin;
use ZQ\SunSearchBundle\Client\Solarium\AdminEndpoint;

/**
 * Class AdminPlugin
 */
class AdminPlugin extends AbstractPlugin
{
    const ACTION_CREATE = 'CREATE';

    /**
     * @var Endpoint
     */
    private $adminEndpoint;


    /**
     * @param Endpoint $endpoint
     */
    public function createCore(Endpoint $endpoint)
    {
        $request = new Request();
        $request->addParams([
            'action' => static::ACTION_CREATE,
            'name'  => $endpoint->getCore(),
            'instanceDir' => $endpoint->getCore(),
            'configSet' => 'data_driven_schema_configs',
        ]);

        $this->client->executeRequest($request, $this->getAdminEndpoint());
    }

    /**
     * @return Endpoint
     */
    protected function getAdminEndpoint()
    {
        if (null === $this->adminEndpoint) {
            $endpoint = $this->client->getEndpoint('admin');
            $this->adminEndpoint = new AdminEndpoint($endpoint->getOptions());
        }

        return $this->adminEndpoint;
    }

    /**
     * @return string
     */
    protected function getAdminPath()
    {
        return $this->getOption('admin_path');
    }
}
