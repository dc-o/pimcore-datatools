<?php
namespace DCO\DataTools\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends AbstractCommand {
    protected function configure()
    {
        $this
            ->setName('datatools:export')
            ->setDescription('automatically exports all main data');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        (new ExportQuantityValuesCommand())->execute($input, $output);
        (new ExportClassesCommand())->execute($input, $output);
        return 0;
    }
}
