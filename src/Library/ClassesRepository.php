<?php
namespace DCO\DataTools\Library;

use Pimcore\Model\DataObject\ClassDefinition;

class ClassesRepository {
    /**
     * returns a list of all already installed classes in pimcore
     * @param $filter string|null Filter for specific classes starting with $filter
     * @param $skipFilter bool Skip exact match of filter
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    public static function getInstalledClasses(string $filter = null, bool $skipFilter = true) : array {
        $classes = \Pimcore\Db::get()->executeQuery('SELECT `name` FROM `classes`')->fetchFirstColumn();
        if (!empty($filter)) {
            return array_filter($classes, function ($v) use ($filter, $skipFilter) { if (strtolower($v) == strtolower($filter) && $skipFilter) return false; return stripos($v, $filter) === 0;  });
        }
        return $classes;
    }

    public static function getInstalledClassIds() : array {
        return \Pimcore\Db::get()->executeQuery('SELECT `id` FROM `classes`')->fetchFirstColumn();
    }

    public static function getAvailableClasses() : array {
        $directories = self::getClassesDirectories();
        $classes = [];
        foreach ($directories as $directory) {
            $directoryList = scandir($directory);
            foreach ($directoryList as $file) {
                if (!str_ends_with($file, '.json'))
                    continue;
                $className = self::getClassNameFromFilename($file);
                if (!isset($classes[$className]))
                    $classes[$className] = [];
                $classes[$className][] = $directory.$file;
            }
        }
        return $classes;
    }
    public static function getClassesDirectories() : array {
        return PimcoreCoreRepository::findDirectories('classes');
    }
    public static function exportClass($className, $filename) : void {
        $config = ClassDefinition::getByName($className);
        if (file_exists($filename)) {
            // check if there were any changes
            try {
                $existingConfig = self::loadClass($filename);
                if (self::compareClassDefinitions($config, $existingConfig))
                    return;
            } catch (\Exception) {
                // configuration is not loadable or empty, recreate
            }
        }
        file_put_contents($filename, json_encode($config, JSON_PRETTY_PRINT));
    }

    private static function loadClass($filename) : object {
        return json_decode(file_get_contents($filename));
    }

    private static function compareClassDefinitions($definition1, $definition2) : bool {
        // if one definition is empty, return false
        if (is_null($definition1) || is_null($definition2))
            return false;
        $definition1->modificationDate = $definition2->modificationDate = null;
        return json_encode($definition1) === json_encode($definition2);
    }

    private static function getClassNameFromFilename($filename) : string {
        $className = $filename;
        if (strpos($className, '/') !== false) {
            $className = pathinfo($className, PATHINFO_FILENAME);
        }
        $className = str_replace('.json', '', $className);
        $aFilename = explode('_', $className, 2);
        if (count($aFilename) == 2 && is_numeric($aFilename[0])) {
            $className = $aFilename[1];
        }
        return $className;
    }

    public static function importClass($filename) : void {
        $className = self::getClassNameFromFilename($filename);
        $existingConfig = ClassDefinition::getByName($className);
        if ($existingConfig === null) {
            $existingConfig = new ClassDefinition();
            $existingConfig->setName($className);
        }
        ClassDefinition\Service::importClassDefinitionFromJson($existingConfig, file_get_contents($filename));
    }
}
