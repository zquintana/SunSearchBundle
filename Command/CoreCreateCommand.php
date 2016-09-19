<?php

namespace ZQ\SunSearchBundle\Command;

use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Exception\OutOfBoundsException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZQ\SunSearchBundle\Client\Solarium\Plugin\AdminPlugin;

/**
 * Class CoreCreateCommand
 */
class CoreCreateCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;


    /**
     * @param PreExecuteRequest $event
     */
    public function preExecuteRequest(PreExecuteRequest $event)
    {
        $output = $this->output;
        $endpoint = $event->getEndpoint();
        $output->writeln(sprintf('Creating core: %s', $endpoint->getCore()));
    }

    /**
     * @param PostExecuteRequest $event
     */
    public function postExecuteRequest(PostExecuteRequest $event)
    {
        $output = $this->output;
        $response = $event->getResponse();
        $statusCode = $response->getStatusCode();

        if (200 >= $statusCode && $statusCode < 400) {
            $output->writeln('<info>Core successfully created.</info>');
        } else {
            $output->writeln('Unable to create core');
            $output->writeln($response->getBody());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sunsearch:create:cores')
            ->setDescription('Creates configured cores');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $sunClient = $this->getContainer()->get('sunsearch.client');
        $solr = $sunClient->getClient();
        /** @var AdminPlugin $adminPlugin */
        $adminPlugin = $solr->getPlugin('SolrBundleAdmin');

        $dispatcher = $solr->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest']);
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, [$this, 'postExecuteRequest']);

        try {
            $solr->getEndpoint('admin');
        } catch (OutOfBoundsException $e) {
            $output->writeln('Admin endpoint isn\'t configured.');

            return;
        }

        foreach ($solr->getEndpoints() as $name => $endpoint) {
            if ('admin' === $name) {
                continue;
            }

            $adminPlugin->createCore($endpoint);
        }
    }
}
