<?php
namespace DCO\DataTools\Tool;

use Pimcore\Model\Asset\Folder;

class AssetTool {
    public static function getFolder($fullPath) {
        $folder = Folder::getByPath($fullPath);
        if (!empty($folder))
            return $folder;
        $aFolder = explode('/', trim($fullPath,'/'));
        $folderKey = array_pop($aFolder);
        $parentFolder = null;
        if ($fullPath == '/' || empty($fullPath)) {
            $parentFolder = Folder::getByPath('/');
        } else {
            $parentFolder = self::getFolder('/'.implode('/', $aFolder));
        }
        $folder = new Folder();
        return $folder
            ->setParent($parentFolder)
            ->setKey($folderKey)
            ->save();
    }
}
