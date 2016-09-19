<?php

namespace ZQ\SunSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command clears the whole index
 */
class ClearIndexCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sunsearch:index:clear')
            ->setDescription('Clear the whole index');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $solr = $this->getContainer()->get('sunsearch.client');

        try {
            $solr->clearIndex();
        } catch (\Exception $e) {
            $output->writeln(sprintf('A error occurs: %s', $e->getMessage()));
        }

        $output->writeln('<info>Index successful cleared.</info>');
    }
}
