<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\ClassesRepository;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportClassesCommand extends AbstractCommand {
    protected function configure()
    {
        $this
            ->setName('datatools:classes:export')
            ->setDescription('automatically exports all classes');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Exporting classes -----');
        $classesAvailable = ClassesRepository::getAvailableClasses();
        $classesDirectories = ClassesRepository::getClassesDirectories();
        foreach (ClassesRepository::getInstalledClasses() as $className) {
            if (!isset($classesAvailable[$className])) {
                if (count($classesDirectories) > 0) {
                    $output->writeln(' > ' . $className . ' doesn\'t exist yet - writing to all directories.');
                    // copy to all classes locations
                    foreach ($classesDirectories as $directory) {
                        ClassesRepository::exportClass($className, $directory . '999_' . $className . '.json');
                    }
                } else {
                    $output->writeln(' > ' . $className . ' doesn\'t exist yet - no exportable directories found.');
                }
            } else {
                $output->writeln(' > '.$className.' exists - updating existing configurations');
                // copy to all existing locations
                foreach ($classesAvailable[$className] as $file) {
                    ClassesRepository::exportClass($className, $file);
                }
            }
        }
        $output->writeln('----- Exporting classes completed. -----');
        return 0;
    }
}
