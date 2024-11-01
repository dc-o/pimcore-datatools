<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\FieldcollectionRepository;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportFieldCollectionsCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:fieldcollections:export')
            ->setDescription('automatically exports all field collections');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Exporting Field Collections -----');
        $fieldcollections = FieldcollectionRepository::getInstalledFieldcollections();
        $fieldcollectionsAvailable = FieldcollectionRepository::getAvailableFieldcollections();
        $fieldcollectionsDirectories = FieldcollectionRepository::getFieldcollectionDirectories();
        foreach ($fieldcollections as $fieldcollection) {
            if (!isset($fieldcollectionsAvailable[$fieldcollection])) {
                if (count($fieldcollectionsDirectories) > 0) {
                    $output->writeln(' > ' . $fieldcollection . ' doesn\'t exist yet - writing to all directories.');
                    foreach ($fieldcollectionsDirectories as $directory) {
                        FieldcollectionRepository::exportFieldcollection($fieldcollection, $directory . '999_' . $fieldcollection . '.json');
                    }
                } else {
                    $output->writeln(' > ' . $fieldcollection . ' doesn\'t exist yet - no exportable directory found.');
                }
            }
            else {
                $output->writeln(' > '.$fieldcollection.' exists - updating existing configurations');
                foreach ($fieldcollectionsAvailable[$fieldcollection] as $file) {
                    FieldcollectionRepository::exportFieldcollection($fieldcollection, $file);
                }
            }
        }
        $output->writeln('----- Exporting Fieldcollections completed. -----');
        return 0;
    }
}
