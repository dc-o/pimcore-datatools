<?php
namespace DCO\DataTools\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends AbstractCommand {
    protected function configure()
    {
        $this
            ->setName('datatools:import')
            ->setDescription('automatically imports all main data');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        (new ImportQuantityValuesCommand())->execute($input, $output);
        (new ImportClassesCommand())->execute($input, $output);
        return 0;
    }
}
