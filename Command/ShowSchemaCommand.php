<?php

namespace ZQ\SunSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowSchemaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sunsearch:schema:show')
            ->setDescription('Show configured entities and their fields');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespaces = $this->getContainer()->get('sunsearch.doctrine.classnameresolver.known_entity_namespaces');
        $metaInformationFactory = $this->getContainer()->get('sunsearch.meta.information.factory');

        foreach ($namespaces->getEntityClassnames() as $classname) {
            try {
                $metaInformation = $metaInformationFactory->loadInformation($classname);
            } catch (\RuntimeException $e) {
                $output->writeln(sprintf('<info>%s</info>', $e->getMessage()));
                continue;
            }

            $output->writeln(sprintf('<comment>%s</comment>', $classname));
            $output->writeln(sprintf('Documentname: %s', $metaInformation->getDocumentName()));
            $output->writeln(sprintf('Document Boost: %s', $metaInformation->getBoost()?$metaInformation->getBoost(): '-'));

            $table = new Table($output);
            $table->setHeaders(array('Property', 'Document Fieldname', 'Boost'));

            foreach ($metaInformation->getFieldMapping() as $documentField => $property) {
                $field = $metaInformation->getField($documentField);

                if ($field === null) {
                    continue;
                }

                $table->addRow(array($property, $documentField, $field->boost));
            }
            $table->render();
        }

    }


}