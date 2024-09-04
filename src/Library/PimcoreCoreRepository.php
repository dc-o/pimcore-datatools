<?php
namespace DCO\DataTools\Library;

class PimcoreCoreRepository {
    /**
     * returns an array of directories for the $directoryName config type
     * @param $directoryName
     * @return array
     */
    public static function findDirectories($directoryName) {
        $directories = [];
        $vendor_base = PIMCORE_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
        if ($vendor = opendir($vendor_base)) {
            while (false !== ($v = readdir($vendor))) {
                if (strpos($v, '.') === 0)
                    continue;
                if (is_dir($vendor_base . $v . DIRECTORY_SEPARATOR)) {
                    if ($vendor_detail = opendir($vendor_base . $v . DIRECTORY_SEPARATOR)) {
                        while (false !== ($d = readdir($vendor_detail))) {
                            if (strpos($d, '.') === 0)
                                continue;
                            if (file_exists($vendor_base . $v . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR)) {
                                $directories[] = $vendor_base . $v . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR;
                            }
                        }
                        closedir($vendor_detail);
                    }
                }
            }
            closedir($vendor);
        }
        return $directories;
    }
}
