<?php
namespace DCO\DataTools\Library;

use Pimcore\Model\DataObject\QuantityValue\Unit;

class UnitRepository {
    public static function getInstalledUnits() : array {
        return \Pimcore\Db::get()->executeQuery('SELECT `id` FROM `quantityvalue_units`')->fetchFirstColumn();
    }

    public static function exportUnit(Unit $unit, string $filename) : void {
        file_put_contents($filename, json_encode(
            [
                'id' => $unit->getId(),
                'abbreviation' => $unit->getAbbreviation(),
                'longname' => $unit->getLongname(),
            ]
        ));
    }

    public static function importUnit(string $filename) : void {
        $data = json_decode(file_get_contents($filename), true);
        $unit = Unit::getById($data['id']);
        if ($unit === null) {
            $unit = new Unit();
        }
        foreach ($data as $name => $value) {
            $unit->{'set'.ucfirst($name)}($value);
        }
        $unit->save();
    }


    public static function getUnitsDirectories() : array {
        return PimcoreCoreRepository::findDirectories('units');
    }

    public static function getAvailableUnits() : array {
        $directories = self::getUnitsDirectories();
        $units = [];
        foreach ($directories as $directory) {
            $directoryList = scandir($directory);
            foreach ($directoryList as $file) {
                if (!str_ends_with($file, '.json'))
                    continue;
                $unitId = self::getUnitIdFromFilename($file);
                if (!isset($units[$unitId]))
                    $units[$unitId] = [];
                $units[$unitId][] = $directory.$file;
            }
        }
        return $units;
    }

    private static function getUnitIdFromFilename($filename) : string {
        $unitId = $filename;
        if (strpos($unitId, '/') !== false) {
            $unitId = pathinfo($unitId, PATHINFO_FILENAME);
        }
        $unitId = str_replace('.json', '', $unitId);
        $aFilename = explode('_', $unitId, 2);
        if (count($aFilename) == 2 && is_numeric($aFilename[0])) {
            $unitId = $aFilename[1];
        }
        return $unitId;
    }
}
