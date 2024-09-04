<?php
namespace DCO\DataTools;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DataToolsBundle extends AbstractPimcoreBundle {
    use PackageVersionTrait;

    protected function getComposerPackageName(): string {
        return 'dc-o/pimcore-datatools';
    }

    public function getJsPaths(): array
    {
        return [];
    }

    public function getCssPaths(): array
    {
        return [];
    }

    public function build(ContainerBuilder $container)
    {

    }


    public function getInstaller(): ?\Pimcore\Extension\Bundle\Installer\InstallerInterface
    {
        return null;
    }
}
