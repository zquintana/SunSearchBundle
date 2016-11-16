<?php

namespace ZQ\SunSearchBundle\Command;

use ZQ\SunSearchBundle\Solarium\QueryType\Core\Query;
use Solarium\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCoresCommand
 */
class CreateCoresCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sunsearch:cores:create')
            ->setDescription('Create solr cores');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $solr        = $this->getContainer()->get('sunsearch.client.adapter');
        $coreManager = $this->getContainer()->get('sunsearch.core_manager');

        foreach ($coreManager->getCores() as $name => $core) {
            $configSet = $core->getConfigSet();
            if (!$configSet) {
                $output->writeln(sprintf('Skipping <info>%s</info> because it\'s missing a config set.', $core->getCoreName()));
                continue;
            }

            $endpoint    = $solr->getEndpoint($core->getConnection());
            /** @var Query $statusQuery */
            $statusQuery = $solr->createQuery(Query::TYPE);
            $statusQuery->status($core->getCoreName());
            $result = $solr->execute($statusQuery, $endpoint);
            $data   = $result->getData();
            if (!empty($data['status'][$core->getCoreName()])) {
                $output->writeln(sprintf('Core of name <info>%s</info> already exists.', $core->getCoreName()));
                continue;
            }

            /** @var Query $query */
            $query = $solr->createQuery(Query::TYPE);
            $query->createWithConfigSet($core->getCoreName(), $configSet);
            try {
                $solr->execute($query, $endpoint);
                $output->writeln(sprintf('Successfully created core <info>%s</info>.', $core->getCoreName()));
            } catch (HttpException $e) {
                $error    = json_decode($e->getBody(), true);

                throw isset($error['error']['msg']) ? new \Exception($error['error']['msg'], 0, $e) : $e;
            }
        }
    }
}
