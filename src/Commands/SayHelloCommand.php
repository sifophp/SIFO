<?php

namespace Sifo\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SayHelloCommand extends Command
{
    public function configure()
    {
        $this->setName('app:say-hello')
            ->setDescription('Says hello.')
            ->setHelp('This command will say you "Hello!"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello!');
    }
}
