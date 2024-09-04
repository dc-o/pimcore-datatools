<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\UnitRepository;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportQuantityValuesCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:units:export')
            ->setDescription('automatically exports all units');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Exporting Units -----');
        $units = UnitRepository::getInstalledUnits();
        $unitsAvailable = UnitRepository::getAvailableUnits();
        $unitsDirectories = UnitRepository::getUnitsDirectories();
        foreach ($units as $unit) {
            $u = Unit::getById($unit);
            if (!isset($unitsAvailable[$unit])) {
                if (count($unitsDirectories) > 0) {
                    $output->writeln(' > ' . $unit . ' doesn\'t exist yet - writing to all directories.');
                    foreach ($unitsDirectories as $directory) {
                        UnitRepository::exportUnit($u, $directory . '999_' . $u->getId() . '.json');
                    }
                } else {
                    $output->writeln(' > ' . $unit . ' doesn\'t exist yet - no exportable directory found.');
                }
            }
            else {
                $output->writeln(' > '.$unit.' exists - updating existing configurations');
                foreach ($unitsAvailable[$unit] as $file) {
                    UnitRepository::exportUnit($u, $file);
                }
            }
        }
        $output->writeln('----- Exporting Units completed. -----');
        return 0;
    }
}
