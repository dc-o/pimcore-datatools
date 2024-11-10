<?php
namespace DCO\DataTools\Library;

class ConfigRepository {

    private $configs = [
        'image_thumbnails' => 'var/config/image_thumbnails'
    ];

    public function getAvailableConfigsForReplacement() {
        $result = [];
        foreach ($this->configs as $source => $target) {
            $directories = PimcoreCoreRepository::findDirectories($source);
            foreach ($directories as $sourceDirectory) {
                $directoryList = scandir($sourceDirectory);
                foreach ($directoryList as $file) {
                    if (!str_ends_with($file, '.replace.yaml'))
                        continue;
                    $targetFilename = substr($file, 0, strlen($file) - 12).'yaml';
                    $result[$sourceDirectory.$file] = PIMCORE_PROJECT_ROOT.DIRECTORY_SEPARATOR.$target.DIRECTORY_SEPARATOR.$targetFilename;
                }
            }
        }
        return $result;
    }
}
