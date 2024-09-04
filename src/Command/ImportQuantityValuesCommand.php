<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\UnitRepository;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportQuantityValuesCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:units:import')
            ->setDescription('automatically imports / overwrites all units');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Importing Units -----');
        $unitPaths = UnitRepository::getAvailableUnits();
        foreach ($unitPaths as $unitName => $unitFiles) {
            $output->writeln(' > '.$unitName.' will be imported');
            UnitRepository::importUnit($unitFiles[0]);
        }
        $output->writeln('----- Importing Units completed -----');
        return 0;
    }
}
