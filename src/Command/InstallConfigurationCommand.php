<?php
namespace DCO\DataTools\Command;

use DCO\DataTools\Library\ConfigRepository;
use DCO\DataTools\Library\PimcoreCoreRepository;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallConfigurationCommand extends AbstractCommand {

    protected function configure()
    {
        $this
            ->setName('datatools:config:install')
            ->setDescription('automatically installs all configurations from all packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $output->writeln('----- Installing configurations -----');
        $repository = new ConfigRepository();
        $configs = $repository->getAvailableConfigsForReplacement();
        foreach ($configs as $source => $target) {
            $output->writeln(' > Replacing '.pathinfo($target, PATHINFO_FILENAME));
            if (!file_exists(dirname($target))) {
                mkdir(dirname($target));
            }
            copy($source, $target);
        }
        $output->writeln('----- Installing configurations completed. -----');
        return 0;
    }
}
