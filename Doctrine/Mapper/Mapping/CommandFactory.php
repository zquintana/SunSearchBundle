<?php

namespace ZQ\SunSearchBundle\Doctrine\Mapper\Mapping;

/**
 * Class CommandFactory
 */
class CommandFactory
{

    /**
     * @var AbstractDocumentCommand[]
     */
    private $commands = array();

    /**
     * @param string $command
     *
     * @return AbstractDocumentCommand
     *
     * @throws \RuntimeException
     */
    public function get($command)
    {
        if (!array_key_exists($command, $this->commands)) {
            throw new \RuntimeException(sprintf('%s is an unknown command', $command));
        }

        return $this->commands[$command];
    }

    /**
     * @param AbstractDocumentCommand $command
     * @param string                  $commandName
     */
    public function add(AbstractDocumentCommand $command, $commandName)
    {
        $this->commands[$commandName] = $command;
    }
}
