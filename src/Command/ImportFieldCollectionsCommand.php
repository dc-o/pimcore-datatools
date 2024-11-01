<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\ClassesRepository;
use DCO\DataTools\Library\FieldcollectionRepository;
use DCO\DataTools\Library\PimcoreCoreRepository;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFieldCollectionsCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:fieldcollections:import')
            ->setDescription('automatically imports / overwrites all field collections');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Importing field collections -----');
        $fieldcollectionsPaths = FieldcollectionRepository::getAvailableFieldcollections();
        foreach ($fieldcollectionsPaths as $fieldcollectionName => $fieldcollectionFiles) {
            $output->writeln(' > '.$fieldcollectionName.' will be imported');
            FieldcollectionRepository::importFieldcollection($fieldcollectionFiles[0]);
        }
        $output->writeln('----- Importing field collections completed. -----');
        return 0;
    }
}
