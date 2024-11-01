<?php
namespace DCO\DataTools\Library;
class FieldcollectionRepository {
    /**
     * returns a list of all already installed field collections in pimcore
     * @param $filter string|null Filter for specific field collections starting with $filter
     * @param $skipFilter bool Skip exact match of filter
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    public static function getInstalledFieldcollections(string $filter = null, bool $skipFilter = true) : array {
        $list = new \Pimcore\Model\DataObject\Fieldcollection\Definition\Listing();
        $list = $list->load();
        $fieldcollections = [];
        foreach ($list as $listItem) {
            $fieldcollections[] = $listItem->getKey();
        }
        if (!empty($filter)) {
            return array_filter($fieldcollections, function ($v) use ($filter, $skipFilter) { if (strtolower($v) == strtolower($filter) && $skipFilter) return false; return stripos($v, $filter) === 0;  });
        }
        return $fieldcollections;
    }

    public static function getAvailableFieldcollections() : array {
        $directories = self::getFieldcollectionDirectories();
        $fieldcollections = [];
        foreach ($directories as $directory) {
            $directoryList = scandir($directory);
            foreach ($directoryList as $file) {
                if (!str_ends_with($file, '.json'))
                    continue;
                $fieldcollectionName = self::getFieldcollectionNameFromFilename($file);
                if (!isset($fieldcollections[$fieldcollectionName]))
                    $fieldcollections[$fieldcollectionName] = [];
                $fieldcollections[$fieldcollectionName][] = $directory.$file;
            }
        }
        return $fieldcollections;
    }
    public static function getFieldcollectionDirectories() : array {
        return PimcoreCoreRepository::findDirectories('fieldcollections');
    }
    public static function exportFieldcollection($key, $filename) : void {

        $config = \Pimcore\Model\DataObject\Fieldcollection\Definition::getByKey($key);

        if (file_exists($filename)) {
            // check if there were any changes
            try {
                $existingConfig = self::loadFieldcollection($filename);
                if (self::compareFieldcollectionDefinitions($config, $existingConfig))
                    return;
            } catch (\Exception) {
                // configuration is not loadable or empty, recreate
            }
        }
        file_put_contents($filename, \Pimcore\Model\DataObject\ClassDefinition\Service::generateFieldCollectionJson($config));
    }

    private static function loadFieldcollection($filename) : object {
        return json_decode(file_get_contents($filename));
    }

    private static function compareFieldcollectionDefinitions($definition1, $definition2) : bool {
        // if one definition is empty, return false
        if (is_null($definition1) || is_null($definition2))
            return false;
        $definition1->modificationDate = $definition2->modificationDate = null;
        return json_encode($definition1) === json_encode($definition2);
    }

    private static function getFieldcollectionNameFromFilename($filename) : string {
        $fieldcollectionName = $filename;
        if (strpos($fieldcollectionName, '/') !== false) {
            $fieldcollectionName = pathinfo($fieldcollectionName, PATHINFO_FILENAME);
        }
        $fieldcollectionName = str_replace('.json', '', $fieldcollectionName);
        $aFilename = explode('_', $fieldcollectionName, 2);
        if (count($aFilename) == 2 && is_numeric($aFilename[0])) {
            $fieldcollectionName = $aFilename[1];
        }
        return $fieldcollectionName;
    }

    public static function importFieldcollection($filename) : void {
        $fieldcollectionName = self::getFieldcollectionNameFromFilename($filename);
        $fieldCollection = \Pimcore\Model\DataObject\Fieldcollection\Definition::getByKey($fieldcollectionName);

        if (empty($fieldCollection)) {
            $fieldCollection = new \Pimcore\Model\DataObject\Fieldcollection\Definition();
            $fieldCollection->setKey($fieldcollectionName);
        }

        \Pimcore\Model\DataObject\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, file_get_contents($filename));
    }
}
