<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\ClassesRepository;
use DCO\DataTools\Library\PimcoreCoreRepository;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportClassesCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:classes:import')
            ->setDescription('automatically imports / overwrites all classes');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Importing classes -----');
        $classesPaths = ClassesRepository::getAvailableClasses();
        foreach ($classesPaths as $className => $classFiles) {
            $output->writeln(' > '.$className.' will be imported');
            ClassesRepository::importClass($classFiles[0]);
        }
        $output->writeln('----- Importing classes completed. -----');
        return 0;
    }
}
